<?php

namespace Wirelabs\FluxChat\Tests\Unit\Events;

use Wirelabs\FluxChat\Events\MessageSent;
use Wirelabs\FluxChat\Models\Conversation;
use Wirelabs\FluxChat\Models\Message;
use Wirelabs\FluxChat\Tests\Support\TestCase;
use Wirelabs\FluxChat\Tests\Support\User;

class MessageSentTest extends TestCase
{
    public function test_message_sent_event_can_be_created()
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

        $event = new MessageSent($message);

        $this->assertInstanceOf(MessageSent::class, $event);
        $this->assertEquals($message->id, $event->message->id);
        $this->assertEquals('Hello!', $event->message->body);
    }

    public function test_message_sent_event_implements_should_broadcast()
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

        $event = new MessageSent($message);

        $this->assertInstanceOf(\Illuminate\Contracts\Broadcasting\ShouldBroadcast::class, $event);
    }

    public function test_message_sent_event_has_correct_broadcast_channels()
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

        $event = new MessageSent($message);
        $channels = $event->broadcastOn();

        $this->assertIsArray($channels);
        $this->assertCount(1, $channels);
        $this->assertInstanceOf(\Illuminate\Broadcasting\Channel::class, $channels[0]);
        $this->assertEquals('fluxchat.conversation.' . $conversation->id, $channels[0]->name);
    }

    public function test_message_sent_event_has_correct_broadcast_data()
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

        $event = new MessageSent($message);
        $data = $event->broadcastWith();

        $this->assertArrayHasKey('message', $data);
        $this->assertArrayHasKey('conversation_id', $data);
        
        $this->assertEquals($message->id, $data['message']['id']);
        $this->assertEquals('Hello!', $data['message']['body']);
        $this->assertEquals($conversation->id, $data['conversation_id']);
        $this->assertEquals('Test User', $data['message']['sender']['name']);
    }
}