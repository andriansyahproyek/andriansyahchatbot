<?php

use App\Http\Controllers\ChatController;
use Illuminate\Support\Facades\Route;

// Main Chat Page
Route::get('/', [ChatController::class, 'index'])->name('chat.index');

// Conversation endpoints
Route::prefix('api')->group(function () {
    Route::get('/conversations', [ChatController::class, 'getConversations'])->name('conversations.index');
    Route::post('/conversations', [ChatController::class, 'storeConversation'])->name('conversations.store');
    Route::get('/conversations/{conversation}', [ChatController::class, 'showConversation'])->name('conversations.show');
    Route::delete('/conversations/{conversation}', [ChatController::class, 'destroyConversation'])->name('conversations.destroy');
    Route::post('/conversations/{conversation}/clear', [ChatController::class, 'clearMessages'])->name('conversations.clear');

    // Message sending endpoint
    Route::post('/chat/send', [ChatController::class, 'sendMessage'])->name('chat.send');
    Route::post('/conversations/{conversation}/messages', [ChatController::class, 'sendMessage'])->name('messages.store');
});
