<?php

namespace Wirelabs\FluxChat\Tests\Unit\Livewire;

use Illuminate\Support\Facades\Auth;
use Livewire\Livewire;
use Wirelabs\FluxChat\Livewire\ChatComponent;
use Wirelabs\FluxChat\Models\Conversation;
use Wirelabs\FluxChat\Models\Message;
use Wirelabs\FluxChat\Tests\Support\TestCase;
use Wirelabs\FluxChat\Tests\Support\User;

class ChatComponentTest extends TestCase
{
    public function test_component_can_be_rendered()
    {
        $user = User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
        ]);

        Auth::login($user);

        $component = Livewire::test(ChatComponent::class)
            ->assertStatus(200);

        $this->assertTrue(true);
    }

    public function test_component_can_select_contact()
    {
        $user = User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
        ]);

        $contact = User::create([
            'name' => 'Contact User',
            'email' => 'contact@example.com',
            'password' => bcrypt('password'),
        ]);

        Auth::login($user);

        $component = Livewire::test(ChatComponent::class, [
            'contacts' => [$contact],
        ]);

        $component->call('selectContact', $contact->id);

        $this->assertEquals($contact->id, $component->get('selectedContact')['id']);
    }

    public function test_component_can_send_message()
    {
        $user = User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
        ]);

        $contact = User::create([
            'name' => 'Contact User',
            'email' => 'contact@example.com',
            'password' => bcrypt('password'),
        ]);

        Auth::login($user);

        $component = Livewire::test(ChatComponent::class, [
            'contacts' => [$contact],
        ]);

        $component->call('selectContact', $contact->id);
        $component->set('newMessage', 'Hello, world!');
        $component->call('sendMessage');

        $this->assertDatabaseHas('fluxchat_messages', [
            'body' => 'Hello, world!',
            'sendable_id' => $user->id,
            'sendable_type' => User::class,
        ]);

        $this->assertEquals('', $component->get('newMessage'));
    }

    public function test_component_wont_send_empty_message()
    {
        $user = User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
        ]);

        $contact = User::create([
            'name' => 'Contact User',
            'email' => 'contact@example.com',
            'password' => bcrypt('password'),
        ]);

        Auth::login($user);

        $component = Livewire::test(ChatComponent::class, [
            'contacts' => [$contact],
        ]);

        $component->call('selectContact', $contact->id);
        $component->set('newMessage', '');
        $component->call('sendMessage');

        $this->assertDatabaseMissing('fluxchat_messages', [
            'sendable_id' => $user->id,
            'sendable_type' => User::class,
        ]);
    }

    public function test_component_can_get_messages()
    {
        $user = User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
        ]);

        $contact = User::create([
            'name' => 'Contact User',
            'email' => 'contact@example.com',
            'password' => bcrypt('password'),
        ]);

        Auth::login($user);

        $conversation = Conversation::create([
            'type' => 'private',
            'is_group' => false,
        ]);

        $conversation->addParticipant($user);
        $conversation->addParticipant($contact);

        $message = $conversation->messages()->create([
            'sendable_id' => $user->id,
            'sendable_type' => User::class,
            'body' => 'Hello!',
            'type' => 'text',
        ]);

        $component = Livewire::test(ChatComponent::class, [
            'contacts' => [$contact],
        ]);

        $component->call('selectContact', $contact->id);

        $messages = $component->get('messages');
        $this->assertCount(1, $messages);
        $this->assertEquals('Hello!', $messages->first()->body);
    }

    public function test_component_can_filter_contacts()
    {
        $user = User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
        ]);

        $contact1 = User::create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => bcrypt('password'),
        ]);

        $contact2 = User::create([
            'name' => 'Jane Smith',
            'email' => 'jane@example.com',
            'password' => bcrypt('password'),
        ]);

        Auth::login($user);

        $component = Livewire::test(ChatComponent::class, [
            'contacts' => [$contact1, $contact2],
        ]);

        $component->set('searchTerm', 'John');

        $filteredContacts = $component->get('filteredContacts');
        $this->assertCount(1, $filteredContacts);
        $this->assertEquals('John Doe', $filteredContacts->first()->name);
    }

    public function test_component_can_change_language()
    {
        $user = User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
        ]);

        Auth::login($user);

        $component = Livewire::test(ChatComponent::class);

        $component->call('changeLanguage', 'nb');

        $this->assertEquals('nb', $component->get('currentLocale'));
    }

    public function test_component_checks_realtime_enabled()
    {
        $user = User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
        ]);

        Auth::login($user);

        $component = Livewire::test(ChatComponent::class);

        $this->assertFalse($component->instance()->isRealtimeEnabled());

        config(['fluxchat.realtime.enabled' => true]);
        config(['broadcasting.default' => 'reverb']);

        $this->assertTrue($component->instance()->isRealtimeEnabled());
    }

    public function test_component_creates_conversation_when_none_exists()
    {
        $user = User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
        ]);

        $contact = User::create([
            'name' => 'Contact User',
            'email' => 'contact@example.com',
            'password' => bcrypt('password'),
        ]);

        Auth::login($user);

        $component = Livewire::test(ChatComponent::class, [
            'contacts' => [$contact],
        ]);

        $component->call('selectContact', $contact->id);
        $component->set('newMessage', 'Hello!');
        $component->call('sendMessage');

        $this->assertDatabaseHas('fluxchat_conversations', [
            'type' => 'private',
            'is_group' => false,
        ]);

        $this->assertDatabaseHas('fluxchat_participants', [
            'participatable_id' => $user->id,
            'participatable_type' => User::class,
        ]);

        $this->assertDatabaseHas('fluxchat_participants', [
            'participatable_id' => $contact->id,
            'participatable_type' => User::class,
        ]);
    }
}