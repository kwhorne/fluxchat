<?php

namespace Kwhorne\FluxChat\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Kwhorne\FluxChat\Events\MessageCreated;
use Kwhorne\FluxChat\Facades\FluxChat;
use Kwhorne\FluxChat\Models\Message;
use Kwhorne\FluxChat\Models\Participant;

class BroadcastMessage implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    protected $auth;

    protected $messagesTable;

    protected $participantsTable;

    public function __construct(public Message $message)
    {
        //
        $this->onQueue(FluxChat::messagesQueue());
        $this->auth = auth()->user();

        // Get table
        $this->messagesTable = (new Message)->getTable();
        $this->participantsTable = (new Participant)->getTable();
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // Broadcast to the conversation channel for all participants
        event(new MessageCreated($this->message));
    }
}
