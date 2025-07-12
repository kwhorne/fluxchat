<?php

namespace Kwhorne\FluxChat\Console\Commands;

use Illuminate\Console\Command;
use Kwhorne\FluxChat\Jobs\DeleteExpiredMessagesJob;

class DeleteExpiredMessage extends Command
{
    protected $signature = 'fluxchat:delete-expired';

    protected $description = 'Deletes expired disappearing messages from conversations';

    public function handle()
    {
        // Run the job that deletes expired messages
        DeleteExpiredMessagesJob::dispatch();

        $this->info('Expired messages have been deleted successfully!');
    }
}
