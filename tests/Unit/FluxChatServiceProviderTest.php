<?php

namespace Wirelabs\FluxChat\Tests\Unit;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Config;
use Livewire\Livewire;
use Wirelabs\FluxChat\Console\Commands\InstallFluxChatCommand;
use Wirelabs\FluxChat\FluxChatServiceProvider;
use Wirelabs\FluxChat\Livewire\ChatComponent;
use Wirelabs\FluxChat\Tests\Support\TestCase;

class FluxChatServiceProviderTest extends TestCase
{
    public function test_service_provider_loads_migrations()
    {
        $this->assertTrue(
            $this->app['migrator']->repositoryExists()
        );

        $migrations = $this->app['migrator']->getMigrationFiles(
            database_path('migrations')
        );

        $this->assertArrayHasKey('2024_01_01_000001_create_fluxchat_conversations_table', $migrations);
        $this->assertArrayHasKey('2024_01_01_000002_create_fluxchat_participants_table', $migrations);
        $this->assertArrayHasKey('2024_01_01_000003_create_fluxchat_messages_table', $migrations);
    }

    public function test_service_provider_loads_views()
    {
        $viewFinder = $this->app['view']->getFinder();
        $paths = $viewFinder->getPaths();

        $this->assertTrue(
            collect($paths)->contains(function ($path) {
                return str_contains($path, 'fluxchat');
            })
        );
    }

    public function test_service_provider_loads_translations()
    {
        $this->assertTrue(
            $this->app['translator']->hasForLocale('fluxchat::messages.send', 'en')
        );
    }

    public function test_service_provider_merges_config()
    {
        $this->assertNotNull(config('fluxchat'));
        $this->assertIsArray(config('fluxchat.realtime'));
        $this->assertIsArray(config('fluxchat.ui'));
        $this->assertIsArray(config('fluxchat.messages'));
        $this->assertIsArray(config('fluxchat.database'));
        $this->assertIsArray(config('fluxchat.broadcasting'));
    }

    public function test_service_provider_registers_livewire_component()
    {
        $this->assertTrue(
            Livewire::isDiscovered(ChatComponent::class)
        );
    }

    public function test_service_provider_registers_commands()
    {
        $this->assertTrue(
            $this->app->bound(InstallFluxChatCommand::class)
        );
    }

    public function test_config_has_correct_default_values()
    {
        $this->assertFalse(config('fluxchat.realtime.enabled'));
        $this->assertEquals('reverb', config('fluxchat.realtime.driver'));
        $this->assertTrue(config('fluxchat.realtime.typing_indicators'));
        $this->assertTrue(config('fluxchat.realtime.online_status'));
        $this->assertEquals(5, config('fluxchat.realtime.auto_refresh_interval'));

        $this->assertEquals('dark', config('fluxchat.ui.theme'));
        $this->assertEquals('sm', config('fluxchat.ui.avatar_size'));
        $this->assertFalse(config('fluxchat.ui.compact_mode'));
        $this->assertTrue(config('fluxchat.ui.show_timestamps'));
        $this->assertTrue(config('fluxchat.ui.auto_scroll'));

        $this->assertEquals(1000, config('fluxchat.messages.max_length'));
        $this->assertFalse(config('fluxchat.messages.file_uploads'));
        $this->assertTrue(config('fluxchat.messages.emoji_support'));
        $this->assertFalse(config('fluxchat.messages.markdown_support'));
    }

    public function test_database_tables_are_correctly_configured()
    {
        $this->assertEquals('fluxchat_conversations', config('fluxchat.database.tables.conversations'));
        $this->assertEquals('fluxchat_messages', config('fluxchat.database.tables.messages'));
        $this->assertEquals('fluxchat_participants', config('fluxchat.database.tables.participants'));
    }

    public function test_broadcasting_config_has_defaults()
    {
        $this->assertEquals('redis', config('fluxchat.broadcasting.connection'));
        $this->assertEquals('default', config('fluxchat.broadcasting.queue'));
        $this->assertEquals('fluxchat', config('fluxchat.broadcasting.channel_prefix'));
    }

    public function test_service_provider_can_publish_assets()
    {
        $provider = new FluxChatServiceProvider($this->app);
        $provider->boot();

        $publishGroups = $this->app['config']->get('app.providers');
        
        // This tests that the service provider doesn't throw errors when publishing
        $this->assertTrue(true);
    }
}