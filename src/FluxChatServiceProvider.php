<?php

namespace Wirelabs\FluxChat;

use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;
use Wirelabs\FluxChat\Console\Commands\InstallFluxChatCommand;
use Wirelabs\FluxChat\Livewire\ChatComponent;

class FluxChatServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any package services.
     */
    public function boot(): void
    {
        // Load migrations
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');

        // Load views
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'fluxchat');

        // Load translations
        $this->loadTranslationsFrom(__DIR__ . '/../resources/lang', 'fluxchat');

        // Publish configuration
        $this->publishes([
            __DIR__ . '/../config/fluxchat.php' => config_path('fluxchat.php'),
        ], 'fluxchat-config');

        // Publish views
        $this->publishes([
            __DIR__ . '/../resources/views' => resource_path('views/vendor/fluxchat'),
        ], 'fluxchat-views');

        // Publish translations
        $this->publishes([
            __DIR__ . '/../resources/lang' => $this->app->langPath('vendor/fluxchat'),
        ], 'fluxchat-lang');

        // Publish migrations
        $this->publishes([
            __DIR__ . '/../database/migrations' => database_path('migrations'),
        ], 'fluxchat-migrations');

        // Register Livewire component
        if (class_exists(Livewire::class)) {
            Livewire::component('fluxchat', ChatComponent::class);
        }

        // Register commands
        if ($this->app->runningInConsole()) {
            $this->commands([
                InstallFluxChatCommand::class,
            ]);
        }
    }

    /**
     * Register any package services.
     */
    public function register(): void
    {
        // Merge configuration
        $this->mergeConfigFrom(
            __DIR__ . '/../config/fluxchat.php',
            'fluxchat'
        );
    }
}
