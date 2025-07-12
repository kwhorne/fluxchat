<?php

namespace Kwhorne\FluxChat\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class SetupNotifications extends Command
{
    protected $signature = 'fluxchat:setup-notifications';

    protected $description = 'Setup FluxChat service worker for notifications';

    public function handle()
    {
        $publicSW = public_path('sw.js');
        $fluxchatSW = public_path('js/fluxchat/sw.js');

        $fluxchatServiceWorkerStub = 'ServiceWorkerJsScript.stub';
        $mainServiceWorkerStub = 'MainServiceWorkerJsScript.stub';

        $this->comment('Setting up Notifications...');

        // Ensure js/fluxchat directory exists
        if (! File::exists(public_path('js/fluxchat'))) {
            File::makeDirectory(public_path('js/fluxchat'), 0755, true);
        }

        // Copy FluxChat SW script if it doesn't exist
        $this->info('Creating FluxChat service worker script...');

        if (File::exists($fluxchatSW)) {
            if ($this->confirm('FluxChat service worker script already exists at `js/fluxchat/sw.js`. Do you want to overwrite it?', false)) {
                File::put($fluxchatSW, $this->getStub($fluxchatServiceWorkerStub));
                $this->info('✅ FluxChat service worker script successfully overwritten at `js/fluxchat/sw.js`.');
            } else {
                $this->info('Existing FluxChat service worker was not overwritten.');
            }
        } else {
            File::put($fluxchatSW, $this->getStub($fluxchatServiceWorkerStub));
            $this->info('✅ FluxChat service worker script successfully created at `js/fluxchat/sw.js`.');
        }

        $this->newLine();

        $this->comment('Creating main service worker script...');

        if (File::exists($publicSW)) {
            $this->error('⚠️ A service worker (sw.js) already exists in the public directory.');
            $this->warn('To use FluxChat notifications, add the following at the top of your service worker file:');
            $this->line("`importScripts('/js/fluxchat/sw.js');`\n");
        } else {
            File::put($publicSW, $this->getStub($mainServiceWorkerStub));
            $this->info('✅ Created `sw.js` in the public directory.');
        }

        $this->info('✅ FluxChat notifications setup complete!');
        $this->line("Note: If you're already using a custom service worker in your application, you need to manually add `importScripts('/js/fluxchat/sw.js');` to your existing service worker file and update the notifications.main_sw_script value in config/fluxchat.php to point to your service worker file.");
        $this->newLine();

        $this->comment('Finally, ensure that `notifications.enabled` is set to true in your FluxChat config.');
    }

    protected function getStub(string $stub)
    {
        return file_get_contents(__DIR__."/../../../stubs/{$stub}");
    }
}
