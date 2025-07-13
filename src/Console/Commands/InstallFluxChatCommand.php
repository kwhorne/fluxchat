<?php

namespace Wirelabs\FluxChat\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class InstallFluxChatCommand extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'fluxchat:install 
                            {--force : Overwrite existing files}
                            {--migrations : Only publish migrations}
                            {--config : Only publish config}
                            {--views : Only publish views}
                            {--lang : Only publish language files}';

    /**
     * The console command description.
     */
    protected $description = 'Install FluxChat package with all necessary files';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('🚀 Installing FluxChat...');

        if ($this->option('migrations') || !$this->hasSpecificOption()) {
            $this->publishMigrations();
        }

        if ($this->option('config') || !$this->hasSpecificOption()) {
            $this->publishConfig();
        }

        if ($this->option('views') || !$this->hasSpecificOption()) {
            $this->publishViews();
        }

        if ($this->option('lang') || !$this->hasSpecificOption()) {
            $this->publishLanguageFiles();
        }

        if (!$this->hasSpecificOption()) {
            $this->displayPostInstallInstructions();
        }

        $this->info('✅ FluxChat installation completed!');

        return self::SUCCESS;
    }

    protected function hasSpecificOption(): bool
    {
        return $this->option('migrations') || 
               $this->option('config') || 
               $this->option('views') || 
               $this->option('lang');
    }

    protected function publishMigrations(): void
    {
        $this->info('📋 Publishing migrations...');
        
        $this->call('vendor:publish', [
            '--tag' => 'fluxchat-migrations',
            '--force' => $this->option('force'),
        ]);
    }

    protected function publishConfig(): void
    {
        $this->info('⚙️  Publishing configuration...');
        
        $this->call('vendor:publish', [
            '--tag' => 'fluxchat-config',
            '--force' => $this->option('force'),
        ]);
    }

    protected function publishViews(): void
    {
        $this->info('🎨 Publishing views...');
        
        $this->call('vendor:publish', [
            '--tag' => 'fluxchat-views',
            '--force' => $this->option('force'),
        ]);
    }

    protected function publishLanguageFiles(): void
    {
        $this->info('🌍 Publishing language files...');
        
        $this->call('vendor:publish', [
            '--tag' => 'fluxchat-lang',
            '--force' => $this->option('force'),
        ]);
    }

    protected function displayPostInstallInstructions(): void
    {
        $this->newLine();
        $this->info('📚 Next steps:');
        $this->line('');
        $this->line('1. Run migrations:');
        $this->line('   <fg=yellow>php artisan migrate</fg=yellow>');
        $this->line('');
        $this->line('2. Add FluxChat component to your view:');
        $this->line('   <fg=yellow><livewire:fluxchat :contacts="$contacts" /></fg=yellow>');
        $this->line('');
        $this->line('3. For real-time messaging, configure Reverb:');
        $this->line('   <fg=yellow>FLUXCHAT_REALTIME_ENABLED=true</fg=yellow>');
        $this->line('   <fg=yellow>BROADCAST_CONNECTION=reverb</fg=yellow>');
        $this->line('');
        $this->line('4. Start Reverb server (for real-time):');
        $this->line('   <fg=yellow>php artisan reverb:start</fg=yellow>');
        $this->line('');
        $this->line('📖 Documentation: https://github.com/wirelabs/fluxchat');
    }
}
