<?php

namespace Workbench\App\Providers;

use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;
use Livewire\LivewireServiceProvider;
use Kwhorne\FluxChat\Livewire\Chat\Chats;
use Kwhorne\FluxChat\FluxChatServiceProvider;

class WorkbenchServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void {}

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
        //  \Livewire\Livewire::forceAssetInjection();

        //  Livewire::component('chat-list', Chats::class);

        // $this->app->register(FluxChatServiceProvider::class);
        // $this->app->register(LivewireServiceProvider::class);

        // Register the FluxChatServiceProvider

    }
}
