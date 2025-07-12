<?php

namespace Kwhorne\FluxChat;

use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;
use Flux\FluxServiceProvider;
use Kwhorne\FluxChat\Console\Commands\InstallFluxChat;
use Kwhorne\FluxChat\Console\Commands\SetupNotifications;
use Kwhorne\FluxChat\Facades\FluxChat as FacadesFluxChat;
use Kwhorne\FluxChat\Livewire\Chat\Chat;
use Kwhorne\FluxChat\Livewire\Chat\Drawer;
use Kwhorne\FluxChat\Livewire\Chat\Group\AddMembers;
use Kwhorne\FluxChat\Livewire\Chat\Group\Info as GroupInfo;
use Kwhorne\FluxChat\Livewire\Chat\Group\Members;
use Kwhorne\FluxChat\Livewire\Chat\Group\Permissions;
use Kwhorne\FluxChat\Livewire\Chat\Info;
use Kwhorne\FluxChat\Livewire\Chats\Chats;
use Kwhorne\FluxChat\Livewire\Modals\Modal;
use Kwhorne\FluxChat\Livewire\New\Chat as NewChat;
use Kwhorne\FluxChat\Livewire\New\Group as NewGroup;
use Kwhorne\FluxChat\Livewire\Pages\Chat as View;
use Kwhorne\FluxChat\Livewire\Pages\Chats as Index;
use Kwhorne\FluxChat\Livewire\Widgets\FluxChat;
use Kwhorne\FluxChat\Middleware\BelongsToConversation;
use Kwhorne\FluxChat\Services\FluxChatService;

class FluxChatServiceProvider extends ServiceProvider
{
    public function boot(): void
    {

        // Register the command if we are using the application via the CLI
        if ($this->app->runningInConsole()) {
            $this->commands([
                InstallFluxChat::class,
                SetupNotifications::class,
            ]);
        }

        $this->loadLivewireComponents();

        $this->loadRoutesFrom(__DIR__.'/../routes/web.php');
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'fluxchat');

        // publish views
        if ($this->app->runningInConsole()) {
            // Publish views
            $this->publishes([
                __DIR__.'/../resources/views' => \resource_path('views/vendor/fluxchat'),
            ], 'fluxchat-views');

            // Publish language files
            $this->publishes([
                __DIR__.'/../lang' => \lang_path('vendor/fluxchat'),
            ], 'fluxchat-translations');

            // publish config
            $this->publishes([
                __DIR__.'/../config/fluxchat.php' => \config_path('fluxchat.php'),
            ], 'fluxchat-config');

            // publish migrations
            $this->publishes([
                __DIR__.'/../database/migrations' => \database_path('migrations'),
            ], 'fluxchat-migrations');
        }

        /* Load channel routes */
        $this->loadRoutesFrom(__DIR__.'/../routes/channels.php');

        // load assets
        $this->loadAssets();

        // load styles
        $this->loadStyles();

        // load middleware
        $this->registerMiddlewares();

        // load translations
        $this->loadTranslationsFrom(__DIR__.'/../lang', 'fluxchat');
    }

    public function register()
    {

        $this->mergeConfigFrom(
            __DIR__.'/../config/fluxchat.php',
            'fluxchat'
        );

        // register facades
        $this->app->singleton('fluxchat', function ($app) {
            return new FluxChatService;
        });

        // Register Flux
        $this->app->register(FluxServiceProvider::class);
    }

    // custom methods for livewire components
    protected function loadLivewireComponents(): void
    {
        // Pages
        Livewire::component('fluxchat.pages.index', Index::class);
        Livewire::component('fluxchat.pages.view', View::class);

        // Chats
        Livewire::component('fluxchat.chats', Chats::class);

        // modal
        Livewire::component('fluxchat.modal', Modal::class);

        Livewire::component('fluxchat.new.chat', NewChat::class);
        Livewire::component('fluxchat.new.group', NewGroup::class);

        // Chat/Group related components
        Livewire::component('fluxchat.chat', Chat::class);
        Livewire::component('fluxchat.chat.info', Info::class);
        Livewire::component('fluxchat.chat.group.info', GroupInfo::class);
        Livewire::component('fluxchat.chat.drawer', Drawer::class);
        Livewire::component('fluxchat.chat.group.add-members', AddMembers::class);
        Livewire::component('fluxchat.chat.group.members', Members::class);
        Livewire::component('fluxchat.chat.group.permissions', Permissions::class);

        // stand alone widget component
        Livewire::component('fluxchat', FluxChat::class);
    }

    protected function registerMiddlewares(): void
    {

        $router = $this->app->make(Router::class);
        $router->aliasMiddleware('belongsToConversation', BelongsToConversation::class);
    }

    protected function loadAssets(): void
    {
        Blade::directive('fluxchatAssets', function () {
            return "<?php if(auth()->check()): ?>
                        <?php 
                            echo Blade::render('@livewire(\'fluxchat.modal\')');
                            echo Blade::render('<x-fluxchat::toast/>');
                            echo Blade::render('<x-fluxchat::notification/>');
                        ?>
                <?php endif; ?>";
        });
    }

    // load assets
    protected function loadStyles(): void
    {

        $primaryColor = FacadesFluxChat::getColor();
        Blade::directive('fluxchatStyles', function () use ($primaryColor) {
            return "<?php echo <<<EOT
                <style>
                    :root {
                        --fc-brand-primary: {$primaryColor};
                        
                        --fc-light-primary: #fff;  /* white */
                        --fc-light-secondary: oklch(0.967 0.003 264.542);/* --color-gray-100 */
                        --fc-light-accent: oklch(0.985 0.002 247.839);/* --color-gray-50 */
                        --fc-light-border: oklch(0.928 0.006 264.531);/* --color-gray-200 */

                        --fc-dark-primary: oklch(0.21 0.034 264.665); /* --color-zinc-900 */
                        --fc-dark-secondary: oklch(0.278 0.033 256.848);/* --color-zinc-800 */
                        --fc-dark-accent: oklch(0.373 0.034 259.733);/* --color-zinc-700 */
                        --fc-dark-border: oklch(0.373 0.034 259.733);/* --color-zinc-700 */
                    }
                    [x-cloak] {
                        display: none !important;
                    }
                </style>
            EOT; ?>";
        });
    }
}
