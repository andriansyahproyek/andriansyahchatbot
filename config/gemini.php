<?php

return [
    'api_key' => env('GEMINI_API_KEY'),
    'model' => env('GEMINI_MODEL', 'gemini-3.5-flash'),
    'base_url' => env('GEMINI_BASE_URL', 'https://generativelanguage.googleapis.com/v1beta'),
    'system_instruction' => env(
        'GEMINI_SYSTEM_INSTRUCTION',
        'Kamu adalah Andriansyah Chatbot, asisten AI yang cerdas, ramah, dan solutif. Jawablah pertanyaan pengguna dengan jelas, sopan, dan terstruktur menggunakan format markdown bila diperlukan.'
    ),
];
