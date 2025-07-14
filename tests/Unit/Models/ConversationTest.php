<?php

namespace Wirelabs\FluxChat\Tests\Unit\Models;

use Wirelabs\FluxChat\Models\Conversation;
use Wirelabs\FluxChat\Models\Message;
use Wirelabs\FluxChat\Models\Participant;
use Wirelabs\FluxChat\Tests\Support\TestCase;
use Wirelabs\FluxChat\Tests\Support\User;

class ConversationTest extends TestCase
{
    public function test_conversation_can_be_created()
    {
        $conversation = Conversation::create([
            'type' => 'private',
            'is_group' => false,
        ]);

        $this->assertInstanceOf(Conversation::class, $conversation);
        $this->assertEquals('private', $conversation->type);
        $this->assertFalse($conversation->is_group);
    }

    public function test_conversation_has_messages_relationship()
    {
        $conversation = Conversation::create([
            'type' => 'private',
            'is_group' => false,
        ]);

        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\HasMany::class, $conversation->messages());
    }

    public function test_conversation_has_participants_relationship()
    {
        $conversation = Conversation::create([
            'type' => 'private',
            'is_group' => false,
        ]);

        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\HasMany::class, $conversation->participants());
    }

    public function test_conversation_can_add_participant()
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

        $conversation->addParticipant($user);

        $this->assertTrue($conversation->hasParticipant($user));
        $this->assertEquals(1, $conversation->participants()->count());
    }

    public function test_conversation_can_remove_participant()
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

        $conversation->addParticipant($user);
        $this->assertTrue($conversation->hasParticipant($user));

        $conversation->removeParticipant($user);
        $this->assertFalse($conversation->hasParticipant($user));
    }

    public function test_conversation_can_get_unread_count()
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

        $conversation->addParticipant($user);

        // Create some messages
        $conversation->messages()->create([
            'sendable_id' => $user->id,
            'sendable_type' => User::class,
            'body' => 'Hello!',
            'type' => 'text',
        ]);

        $conversation->messages()->create([
            'sendable_id' => $user->id,
            'sendable_type' => User::class,
            'body' => 'How are you?',
            'type' => 'text',
        ]);

        $unreadCount = $conversation->getUnreadCount($user);
        $this->assertEquals(2, $unreadCount);
    }

    public function test_conversation_can_mark_as_read()
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

        $conversation->addParticipant($user);

        $conversation->messages()->create([
            'sendable_id' => $user->id,
            'sendable_type' => User::class,
            'body' => 'Hello!',
            'type' => 'text',
        ]);

        $this->assertEquals(1, $conversation->getUnreadCount($user));

        $conversation->markAsRead($user);
        $this->assertEquals(0, $conversation->getUnreadCount($user));
    }

    public function test_conversation_casts_attributes_correctly()
    {
        $conversation = Conversation::create([
            'type' => 'private',
            'is_group' => true,
            'settings' => ['theme' => 'dark'],
        ]);

        $this->assertIsBool($conversation->is_group);
        $this->assertIsArray($conversation->settings);
        $this->assertEquals('dark', $conversation->settings['theme']);
    }

    public function test_conversation_has_latest_message_relationship()
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

        $message1 = $conversation->messages()->create([
            'sendable_id' => $user->id,
            'sendable_type' => User::class,
            'body' => 'First message',
            'type' => 'text',
        ]);

        sleep(1);

        $message2 = $conversation->messages()->create([
            'sendable_id' => $user->id,
            'sendable_type' => User::class,
            'body' => 'Latest message',
            'type' => 'text',
        ]);

        $latestMessage = $conversation->latestMessage;
        $this->assertEquals($message2->id, $latestMessage->id);
        $this->assertEquals('Latest message', $latestMessage->body);
    }
}