<?php

namespace Wirelabs\FluxChat\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Wirelabs\FluxChat\Models\Message;

class MessageSent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $message;
    public $conversationId;

    public function __construct(Message $message)
    {
        $this->message = $message;
        $this->conversationId = $message->conversation_id;
    }

    /**
     * Get the channels the event should broadcast on.
     */
    public function broadcastOn(): array
    {
        $prefix = config('fluxchat.broadcasting.channel_prefix', 'fluxchat');
        
        return [
            new Channel($prefix . '.conversation.' . $this->conversationId),
        ];
    }

    /**
     * Get the data to broadcast.
     */
    public function broadcastWith(): array
    {
        return [
            'message' => [
                'id' => $this->message->id,
                'body' => $this->message->body,
                'sendable_type' => $this->message->sendable_type,
                'sendable_id' => $this->message->sendable_id,
                'created_at' => $this->message->created_at->toISOString(),
                'sender' => $this->message->sendable ? [
                    'name' => $this->message->sendable->name ?? 'Unknown',
                ] : null,
            ],
            'conversation_id' => $this->conversationId,
        ];
    }

    /**
     * Get the event name for broadcasting.
     */
    public function broadcastAs(): string
    {
        return 'message.sent';
    }
}
