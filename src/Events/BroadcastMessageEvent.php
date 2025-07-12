<?php

namespace Kwhorne\FluxChat\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Kwhorne\FluxChat\Models\Conversation;
use Kwhorne\FluxChat\Models\Message;

class BroadcastMessageEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public Message $message, public Conversation $conversation)
    {

        // Log::info($participant);
    }
}
