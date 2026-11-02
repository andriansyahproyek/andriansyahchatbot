<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use App\Models\Message;
use App\Services\GeminiService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ChatController extends Controller
{
    protected GeminiService $geminiService;

    public function __construct(GeminiService $geminiService)
    {
        $this->geminiService = $geminiService;
    }

    /**
     * Show main chat interface.
     */
    public function index(): View
    {
        $conversations = Conversation::withCount('messages')
            ->latest('updated_at')
            ->get();

        return view('chat.index', compact('conversations'));
    }

    /**
     * List all conversations as JSON.
     */
    public function getConversations(): JsonResponse
    {
        $conversations = Conversation::withCount('messages')
            ->latest('updated_at')
            ->get();

        return response()->json([
            'conversations' => $conversations,
        ]);
    }

    /**
     * Get a specific conversation and its messages.
     */
    public function showConversation(Conversation $conversation): JsonResponse
    {
        $messages = $conversation->messages()->orderBy('created_at', 'asc')->get();

        return response()->json([
            'conversation' => $conversation,
            'messages' => $messages,
        ]);
    }

    /**
     * Create a new conversation.
     */
    public function storeConversation(Request $request): JsonResponse
    {
        $request->validate([
            'title' => 'nullable|string|max:100',
        ]);

        $conversation = Conversation::create([
            'title' => $request->input('title', 'Percakapan Baru'),
        ]);

        return response()->json([
            'success' => true,
            'conversation' => $conversation,
        ], 201);
    }

    /**
     * Delete a conversation.
     */
    public function destroyConversation(Conversation $conversation): JsonResponse
    {
        $conversation->delete();

        return response()->json([
            'success' => true,
            'message' => 'Percakapan berhasil dihapus.',
        ]);
    }

    /**
     * Send message and get Gemini AI response.
     */
    public function sendMessage(Request $request, ?Conversation $conversation = null): JsonResponse
    {
        $request->validate([
            'message' => 'required|string|max:5000',
            'conversation_id' => 'nullable|exists:conversations,id',
        ]);

        $content = trim($request->input('message'));
        $conversationId = $request->input('conversation_id');

        if (!$conversation && $conversationId) {
            $conversation = Conversation::find($conversationId);
        }

        $isNewConversation = false;
        if (!$conversation) {
            $conversation = Conversation::create([
                'title' => 'Percakapan Baru',
            ]);
            $isNewConversation = true;
        }

        // Save user message to database
        $userMessage = $conversation->messages()->create([
            'role' => 'user',
            'content' => $content,
        ]);

        // Auto-update conversation title if it's default or new
        if ($isNewConversation || $conversation->title === 'Percakapan Baru' || $conversation->messages()->count() <= 2) {
            $generatedTitle = $this->geminiService->generateTitle($content);
            $conversation->update(['title' => $generatedTitle]);
        }

        // Fetch recent messages for context (up to 20 messages)
        $contextMessages = $conversation->messages()
            ->orderBy('created_at', 'asc')
            ->take(20)
            ->get()
            ->map(function ($msg) {
                return [
                    'role' => $msg->role,
                    'content' => $msg->content,
                ];
            })
            ->toArray();

        try {
            // Call Gemini AI
            $aiResult = $this->geminiService->chat($contextMessages);

            // Save AI message to database
            $assistantMessage = $conversation->messages()->create([
                'role' => 'model',
                'content' => $aiResult['text'],
                'tokens' => $aiResult['total_tokens'] ?? null,
            ]);

            // Touch conversation updated_at
            $conversation->touch();

            return response()->json([
                'success' => true,
                'conversation' => $conversation->fresh(),
                'user_message' => $userMessage,
                'assistant_message' => $assistantMessage,
                'model' => $aiResult['model'] ?? null,
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'user_message' => $userMessage,
            ], 500);
        }
    }

    /**
     * Clear all messages in a conversation.
     */
    public function clearMessages(Conversation $conversation): JsonResponse
    {
        $conversation->messages()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Pesan dalam percakapan telah dibersihkan.',
        ]);
    }
}
