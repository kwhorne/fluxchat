<?php

use Livewire\Livewire;
use Kwhorne\FluxChat\Livewire\Chat\Chat;
use Kwhorne\FluxChat\Livewire\Chats\Chats;
use Kwhorne\FluxChat\Livewire\Widgets\FluxChat;
use Kwhorne\FluxChat\Models\Conversation;
use Workbench\App\Models\User;

test('user must be authenticated', function () {

    $conversation = Conversation::factory()->create();
    Livewire::test(FluxChat::class)
        ->assertStatus(401);
});

test('it renders livewire ChatList component', function () {
    $auth = User::factory()->create();
    $conversation = Conversation::factory()->create();
    $response = Livewire::actingAs($auth)->test(FluxChat::class);
    $response->assertSeeLivewire(Chats::class);

});

test('it doest not render livewire ChatBox component', function () {
    $auth = User::factory()->create();
    $conversation = Conversation::factory()->create();
    $response = Livewire::actingAs($auth)->test(FluxChat::class);
    $response->assertDontSeeLivewire(Chat::class);

});

test('it shows label "Send private photos and messages" ', function () {
    $auth = User::factory()->create();
    $conversation = Conversation::factory()->create();
    $response = Livewire::actingAs($auth)->test(FluxChat::class);
    $response->assertSee('Select a conversation to start messaging');

});

test('it renders Chat when "openChatWidget" event is selected ', function () {
    $auth = User::factory()->create();

    $conversation = $auth->createConversationWith(User::factory()->create());
    $response = Livewire::actingAs($auth)->test(FluxChat::class);

    $response->assertDontSeeLivewire(Chat::class);

    $response->dispatch('openChatWidget', conversation: $conversation->id);

    // dd($response);
    $response->assertSeeLivewire(Chat::class);

});

test('it removes Chat when "closeChatWidget" event is selected ', function () {
    $auth = User::factory()->create();

    $conversation = $auth->createConversationWith(User::factory()->create());
    $response = Livewire::actingAs($auth)->test(FluxChat::class);

    // assert
    $response->assertDontSeeLivewire(Chat::class);

    // open
    $response->dispatch('openChatWidget', conversation: $conversation->id);

    // assert
    $response->assertSeeLivewire(Chat::class);

    // open
    $response->dispatch('closeChatWidget');

    // assert
    $response->assertDontSeeLivewire(Chat::class);

});
