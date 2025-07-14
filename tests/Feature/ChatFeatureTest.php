<?php

namespace Wirelabs\FluxChat\Tests\Feature;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Event;
use Livewire\Livewire;
use Wirelabs\FluxChat\Events\MessageSent;
use Wirelabs\FluxChat\Livewire\ChatComponent;
use Wirelabs\FluxChat\Models\Conversation;
use Wirelabs\FluxChat\Tests\Support\TestCase;
use Wirelabs\FluxChat\Tests\Support\User;

class ChatFeatureTest extends TestCase
{
    public function test_users_can_have_conversation()
    {
        $user1 = User::create([
            'name' => 'User One',
            'email' => 'user1@example.com',
            'password' => bcrypt('password'),
        ]);

        $user2 = User::create([
            'name' => 'User Two',
            'email' => 'user2@example.com',
            'password' => bcrypt('password'),
        ]);

        Auth::login($user1);

        // User 1 starts conversation with User 2
        $component = Livewire::test(ChatComponent::class, [
            'contacts' => [$user2],
        ]);

        $component->call('selectContact', $user2->id);
        $component->set('newMessage', 'Hello from User 1!');
        $component->call('sendMessage');

        // Verify conversation was created
        $this->assertDatabaseHas('fluxchat_conversations', [
            'type' => 'private',
            'is_group' => false,
        ]);

        // Verify message was sent
        $this->assertDatabaseHas('fluxchat_messages', [
            'body' => 'Hello from User 1!',
            'sendable_id' => $user1->id,
            'sendable_type' => User::class,
        ]);

        // Verify both users are participants
        $conversation = Conversation::first();
        $this->assertTrue($conversation->hasParticipant($user1));
        $this->assertTrue($conversation->hasParticipant($user2));
    }

    public function test_message_sent_event_is_dispatched()
    {
        Event::fake();

        $user1 = User::create([
            'name' => 'User One',
            'email' => 'user1@example.com',
            'password' => bcrypt('password'),
        ]);

        $user2 = User::create([
            'name' => 'User Two',
            'email' => 'user2@example.com',
            'password' => bcrypt('password'),
        ]);

        Auth::login($user1);

        config(['fluxchat.realtime.enabled' => true]);
        config(['broadcasting.default' => 'reverb']);

        $component = Livewire::test(ChatComponent::class, [
            'contacts' => [$user2],
        ]);

        $component->call('selectContact', $user2->id);
        $component->set('newMessage', 'Hello!');
        $component->call('sendMessage');

        Event::assertDispatched(MessageSent::class);
    }

    public function test_conversation_is_marked_as_read_when_selected()
    {
        $user1 = User::create([
            'name' => 'User One',
            'email' => 'user1@example.com',
            'password' => bcrypt('password'),
        ]);

        $user2 = User::create([
            'name' => 'User Two',
            'email' => 'user2@example.com',
            'password' => bcrypt('password'),
        ]);

        Auth::login($user1);

        // Create a conversation with messages
        $conversation = Conversation::create([
            'type' => 'private',
            'is_group' => false,
        ]);

        $conversation->addParticipant($user1);
        $conversation->addParticipant($user2);

        $conversation->messages()->create([
            'sendable_id' => $user2->id,
            'sendable_type' => User::class,
            'body' => 'Hello!',
            'type' => 'text',
        ]);

        // Initially should have unread messages
        $this->assertEquals(1, $conversation->getUnreadCount($user1));

        $component = Livewire::test(ChatComponent::class, [
            'contacts' => [$user2],
        ]);

        $component->call('selectContact', $user2->id);

        // After selecting contact, conversation should be marked as read
        $this->assertEquals(0, $conversation->fresh()->getUnreadCount($user1));
    }

    public function test_multiple_messages_in_conversation()
    {
        $user1 = User::create([
            'name' => 'User One',
            'email' => 'user1@example.com',
            'password' => bcrypt('password'),
        ]);

        $user2 = User::create([
            'name' => 'User Two',
            'email' => 'user2@example.com',
            'password' => bcrypt('password'),
        ]);

        Auth::login($user1);

        $component = Livewire::test(ChatComponent::class, [
            'contacts' => [$user2],
        ]);

        $component->call('selectContact', $user2->id);

        // Send multiple messages
        $component->set('newMessage', 'Message 1');
        $component->call('sendMessage');

        $component->set('newMessage', 'Message 2');
        $component->call('sendMessage');

        $component->set('newMessage', 'Message 3');
        $component->call('sendMessage');

        // Should have 3 messages in the conversation
        $messages = $component->get('messages');
        $this->assertCount(3, $messages);

        // Messages should be in correct order
        $this->assertEquals('Message 1', $messages[0]->body);
        $this->assertEquals('Message 2', $messages[1]->body);
        $this->assertEquals('Message 3', $messages[2]->body);
    }

    public function test_contact_search_functionality()
    {
        $user = User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
        ]);

        $contacts = [
            User::create(['name' => 'Alice Johnson', 'email' => 'alice@example.com', 'password' => bcrypt('password')]),
            User::create(['name' => 'Bob Smith', 'email' => 'bob@example.com', 'password' => bcrypt('password')]),
            User::create(['name' => 'Charlie Brown', 'email' => 'charlie@example.com', 'password' => bcrypt('password')]),
        ];

        Auth::login($user);

        $component = Livewire::test(ChatComponent::class, [
            'contacts' => $contacts,
        ]);

        // Search for "Alice"
        $component->set('searchTerm', 'Alice');
        $filteredContacts = $component->get('filteredContacts');
        $this->assertCount(1, $filteredContacts);
        $this->assertEquals('Alice Johnson', $filteredContacts->first()->name);

        // Search for "Smith"
        $component->set('searchTerm', 'Smith');
        $filteredContacts = $component->get('filteredContacts');
        $this->assertCount(1, $filteredContacts);
        $this->assertEquals('Bob Smith', $filteredContacts->first()->name);

        // Search for partial match
        $component->set('searchTerm', 'o');
        $filteredContacts = $component->get('filteredContacts');
        $this->assertCount(2, $filteredContacts); // Alice Johnson and Bob Smith
    }

    public function test_conversation_reuse_between_same_participants()
    {
        $user1 = User::create([
            'name' => 'User One',
            'email' => 'user1@example.com',
            'password' => bcrypt('password'),
        ]);

        $user2 = User::create([
            'name' => 'User Two',
            'email' => 'user2@example.com',
            'password' => bcrypt('password'),
        ]);

        Auth::login($user1);

        $component = Livewire::test(ChatComponent::class, [
            'contacts' => [$user2],
        ]);

        $component->call('selectContact', $user2->id);
        $component->set('newMessage', 'First message');
        $component->call('sendMessage');

        $firstConversationId = Conversation::first()->id;

        // Send another message - should use same conversation
        $component->set('newMessage', 'Second message');
        $component->call('sendMessage');

        // Should still have only one conversation
        $this->assertEquals(1, Conversation::count());
        $this->assertEquals($firstConversationId, Conversation::first()->id);

        // But should have two messages
        $this->assertEquals(2, Conversation::first()->messages()->count());
    }
}