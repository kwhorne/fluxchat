<?php

namespace Wirelabs\FluxChat\Tests\Support;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\LivewireServiceProvider;
use Orchestra\Testbench\TestCase as OrchestraTestCase;
use Wirelabs\FluxChat\FluxChatServiceProvider;

abstract class TestCase extends OrchestraTestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->loadLaravelMigrations();
        $this->artisan('migrate', ['--database' => 'testing']);
    }

    protected function getPackageProviders($app)
    {
        return [
            LivewireServiceProvider::class,
            FluxChatServiceProvider::class,
        ];
    }

    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('database.default', 'testing');
        $app['config']->set('database.connections.testing', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);

        $app['config']->set('auth.providers.users.model', User::class);
        $app['config']->set('fluxchat.realtime.enabled', false);
    }

    protected function defineDatabaseMigrations()
    {
        $this->loadMigrationsFrom(__DIR__ . '/../../database/migrations');
    }
}