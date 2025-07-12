<?php

use Illuminate\Support\Facades\Route;
use Kwhorne\FluxChat\Livewire\Pages\Chat;
use Kwhorne\FluxChat\Livewire\Pages\Chats;

Route::middleware(config('fluxchat.routes.middleware'))
    ->prefix(config('fluxchat.routes.prefix'))
    ->group(function () {
        Route::get('/', Chats::class)->name('chats');
        Route::get('/{conversation}', Chat::class)->middleware('belongsToConversation')->name('chat');
    });
