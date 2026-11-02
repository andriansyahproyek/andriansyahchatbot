<?php

namespace Tests\Feature;

use App\Models\Conversation;
use App\Models\Message;
use App\Services\GeminiService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Tests\TestCase;

class ChatbotTest extends TestCase
{
    use RefreshDatabase;

    public function test_main_chat_page_is_accessible(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('Andriansyah Chatbot');
        $response->assertSee('Laravel 12');
        $response->assertSee('MySQL');
    }

    public function test_can_create_conversation(): void
    {
        $response = $this->postJson('/api/conversations', [
            'title' => 'Diskusi Framework',
        ]);

        $response->assertStatus(201);
        $response->assertJson([
            'success' => true,
            'conversation' => [
                'title' => 'Diskusi Framework',
            ],
        ]);

        $this->assertDatabaseHas('conversations', [
            'title' => 'Diskusi Framework',
        ]);
    }

    public function test_can_list_conversations(): void
    {
        Conversation::create(['title' => 'Percakapan 1']);
        Conversation::create(['title' => 'Percakapan 2']);

        $response = $this->getJson('/api/conversations');

        $response->assertStatus(200);
        $response->assertJsonCount(2, 'conversations');
    }

    public function test_can_show_conversation_and_messages(): void
    {
        $conversation = Conversation::create(['title' => 'Test Percakapan']);
        $conversation->messages()->create([
            'role' => 'user',
            'content' => 'Halo Gemini',
        ]);

        $response = $this->getJson("/api/conversations/{$conversation->id}");

        $response->assertStatus(200);
        $response->assertJsonPath('conversation.id', $conversation->id);
        $response->assertJsonCount(1, 'messages');
    }

    public function test_can_send_message_and_save_to_database(): void
    {
        $mock = Mockery::mock(GeminiService::class);
        $mock->shouldReceive('generateTitle')
            ->once()
            ->andReturn('Fakta Laravel');
        $mock->shouldReceive('chat')
            ->once()
            ->andReturn([
                'success' => true,
                'text' => 'Laravel adalah framework PHP yang elegan.',
                'model' => 'gemini-3.5-flash',
                'prompt_tokens' => 10,
                'candidates_tokens' => 15,
                'total_tokens' => 25,
            ]);

        $this->app->instance(GeminiService::class, $mock);

        $response = $this->postJson('/api/chat/send', [
            'message' => 'Ceritakan tentang Laravel',
        ]);

        $response->assertStatus(200);
        $response->assertJsonPath('success', true);
        $response->assertJsonPath('assistant_message.content', 'Laravel adalah framework PHP yang elegan.');

        $this->assertDatabaseHas('conversations', [
            'title' => 'Fakta Laravel',
        ]);

        $this->assertDatabaseHas('messages', [
            'role' => 'user',
            'content' => 'Ceritakan tentang Laravel',
        ]);

        $this->assertDatabaseHas('messages', [
            'role' => 'model',
            'content' => 'Laravel adalah framework PHP yang elegan.',
        ]);
    }

    public function test_can_clear_conversation_messages(): void
    {
        $conversation = Conversation::create(['title' => 'Chat to Clear']);
        $conversation->messages()->create([
            'role' => 'user',
            'content' => 'Halo',
        ]);

        $this->assertEquals(1, $conversation->messages()->count());

        $response = $this->postJson("/api/conversations/{$conversation->id}/clear");

        $response->assertStatus(200);
        $this->assertEquals(0, $conversation->messages()->count());
    }

    public function test_can_delete_conversation(): void
    {
        $conversation = Conversation::create(['title' => 'Chat to Delete']);
        $conversation->messages()->create([
            'role' => 'user',
            'content' => 'Pesan 1',
        ]);

        $response = $this->deleteJson("/api/conversations/{$conversation->id}");

        $response->assertStatus(200);
        $this->assertDatabaseMissing('conversations', ['id' => $conversation->id]);
        $this->assertDatabaseMissing('messages', ['conversation_id' => $conversation->id]);
    }
}
