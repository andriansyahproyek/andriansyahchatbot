<?php

namespace App\Services;

use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class GeminiService
{
    protected string $apiKey;
    protected string $model;
    protected string $baseUrl;
    protected string $defaultSystemInstruction;

    public function __construct()
    {
        $this->apiKey = config('gemini.api_key', '');
        $this->model = config('gemini.model', 'gemini-flash-latest');
        $this->baseUrl = config('gemini.base_url', 'https://generativelanguage.googleapis.com/v1beta');
        $this->defaultSystemInstruction = config(
            'gemini.system_instruction',
            'Kamu adalah asisten AI yang cerdas, ramah, dan membantu. Jawablah dengan bahasa Indonesia yang jelas, santun, dan terstruktur menggunakan markdown.'
        );
    }

    /**
     * Send conversation history to Gemini API and receive a response.
     *
     * @param  array  $messages Array of ['role' => 'user'|'model', 'content' => string]
     * @param  string|null  $systemInstruction Custom system instruction
     * @return array
     * @throws Exception
     */
    public function chat(array $messages, ?string $systemInstruction = null): array
    {
        if (empty($this->apiKey)) {
            throw new Exception('GEMINI_API_KEY belum dikonfigurasi di file .env');
        }

        $formattedContents = [];
        foreach ($messages as $msg) {
            $role = ($msg['role'] === 'model' || $msg['role'] === 'assistant') ? 'model' : 'user';
            $content = trim($msg['content'] ?? '');

            if ($content !== '') {
                $formattedContents[] = [
                    'role' => $role,
                    'parts' => [
                        ['text' => $content]
                    ]
                ];
            }
        }

        if (empty($formattedContents)) {
            throw new Exception('Pesan percakapan tidak boleh kosong.');
        }

        // Gemini requires the first message in contents to have role 'user'
        if ($formattedContents[0]['role'] !== 'user') {
            array_unshift($formattedContents, [
                'role' => 'user',
                'parts' => [['text' => 'Halo']]
            ]);
        }

        $instruction = $systemInstruction ?: $this->defaultSystemInstruction;

        $payload = [
            'contents' => $formattedContents,
        ];

        if (!empty($instruction)) {
            $payload['system_instruction'] = [
                'parts' => [
                    ['text' => $instruction]
                ]
            ];
        }

        $endpoint = "{$this->baseUrl}/models/{$this->model}:generateContent?key={$this->apiKey}";

        // Make HTTP request with retry logic for intermittent 503 high demand spikes
        $response = Http::timeout(60)
            ->retry(2, 1000, function ($exception, $request) {
                return $exception instanceof Exception;
            })
            ->post($endpoint, $payload);

        if (!$response->successful()) {
            $errorData = $response->json();
            $errorMessage = $errorData['error']['message'] ?? 'Terjadi kesalahan saat menghubungi API Gemini (' . $response->status() . ')';
            Log::error('Gemini API Error', ['status' => $response->status(), 'response' => $errorData]);
            throw new Exception($errorMessage);
        }

        $responseData = $response->json();
        $candidates = $responseData['candidates'] ?? [];

        if (empty($candidates)) {
            throw new Exception('Gemini tidak memberikan respon kandidat yang valid.');
        }

        $responseText = $candidates[0]['content']['parts'][0]['text'] ?? '';
        $usage = $responseData['usageMetadata'] ?? [];

        return [
            'success' => true,
            'text' => $responseText,
            'model' => $responseData['modelVersion'] ?? $this->model,
            'prompt_tokens' => $usage['promptTokenCount'] ?? null,
            'candidates_tokens' => $usage['candidatesTokenCount'] ?? null,
            'total_tokens' => $usage['totalTokenCount'] ?? null,
        ];
    }

    /**
     * Generate a short conversation title from the first prompt.
     *
     * @param  string  $prompt
     * @return string
     */
    public function generateTitle(string $prompt): string
    {
        try {
            $endpoint = "{$this->baseUrl}/models/{$this->model}:generateContent?key={$this->apiKey}";
            $response = Http::timeout(15)->post($endpoint, [
                'contents' => [
                    [
                        'role' => 'user',
                        'parts' => [
                            [
                                'text' => "Buatkan judul singkat maksimal 4-5 kata dalam bahasa Indonesia tanpa tanda petik untuk percakapan yang diawali dengan: \"{$prompt}\""
                            ]
                        ]
                    ]
                ]
            ]);

            if ($response->successful()) {
                $title = trim($response->json()['candidates'][0]['content']['parts'][0]['text'] ?? '');
                $title = str_replace(['"', "'", "\n", "\r", "*"], '', $title);
                if (!empty($title)) {
                    return Str::limit($title, 45, '');
                }
            }
        } catch (\Throwable $e) {
            // Fallback to text snippet if title generation fails
        }

        return Str::limit(trim($prompt), 35);
    }
}
