<?php

namespace Wirelabs\FluxChat\Tests\Unit\Models;

use Wirelabs\FluxChat\Models\Conversation;
use Wirelabs\FluxChat\Models\Message;
use Wirelabs\FluxChat\Tests\Support\TestCase;
use Wirelabs\FluxChat\Tests\Support\User;

class MessageTest extends TestCase
{
    public function test_message_can_be_created()
    {
        $conversation = Conversation::create([
            'type' => 'private',
            'is_group' => false,
        ]);

        $user = User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
        ]);

        $message = Message::create([
            'conversation_id' => $conversation->id,
            'sendable_id' => $user->id,
            'sendable_type' => User::class,
            'body' => 'Hello, world!',
            'type' => 'text',
        ]);

        $this->assertInstanceOf(Message::class, $message);
        $this->assertEquals('Hello, world!', $message->body);
        $this->assertEquals('text', $message->type);
        $this->assertEquals($conversation->id, $message->conversation_id);
    }

    public function test_message_belongs_to_conversation()
    {
        $conversation = Conversation::create([
            'type' => 'private',
            'is_group' => false,
        ]);

        $user = User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
        ]);

        $message = $conversation->messages()->create([
            'sendable_id' => $user->id,
            'sendable_type' => User::class,
            'body' => 'Hello!',
            'type' => 'text',
        ]);

        $this->assertEquals($conversation->id, $message->conversation->id);
    }

    public function test_message_has_sendable_relationship()
    {
        $conversation = Conversation::create([
            'type' => 'private',
            'is_group' => false,
        ]);

        $user = User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
        ]);

        $message = $conversation->messages()->create([
            'sendable_id' => $user->id,
            'sendable_type' => User::class,
            'body' => 'Hello!',
            'type' => 'text',
        ]);

        $this->assertEquals($user->id, $message->sendable->id);
        $this->assertEquals($user->name, $message->sendable->name);
    }

    public function test_message_casts_attributes_correctly()
    {
        $conversation = Conversation::create([
            'type' => 'private',
            'is_group' => false,
        ]);

        $user = User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
        ]);

        $message = $conversation->messages()->create([
            'sendable_id' => $user->id,
            'sendable_type' => User::class,
            'body' => 'Hello!',
            'type' => 'text',
            'data' => ['edited' => true],
        ]);

        $this->assertIsArray($message->data);
        $this->assertTrue($message->data['edited']);
    }

    public function test_message_has_is_edited_method()
    {
        $conversation = Conversation::create([
            'type' => 'private',
            'is_group' => false,
        ]);

        $user = User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
        ]);

        $message = $conversation->messages()->create([
            'sendable_id' => $user->id,
            'sendable_type' => User::class,
            'body' => 'Hello!',
            'type' => 'text',
        ]);

        $this->assertFalse($message->isEdited());

        $message->update(['edited_at' => now()]);
        $this->assertTrue($message->isEdited());
    }

    public function test_message_has_soft_deletes()
    {
        $conversation = Conversation::create([
            'type' => 'private',
            'is_group' => false,
        ]);

        $user = User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
        ]);

        $message = $conversation->messages()->create([
            'sendable_id' => $user->id,
            'sendable_type' => User::class,
            'body' => 'Hello!',
            'type' => 'text',
        ]);

        $messageId = $message->id;

        $message->delete();

        $this->assertSoftDeleted('fluxchat_messages', ['id' => $messageId]);
        $this->assertNotNull($message->fresh()->deleted_at);
    }
}