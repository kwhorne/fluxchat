<?php

namespace Wirelabs\FluxChat\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Message extends Model
{
    use SoftDeletes;

    protected $table = 'fluxchat_messages';

    protected $fillable = [
        'conversation_id',
        'sendable_type',
        'sendable_id',
        'body',
        'type',
        'data',
        'reply_to_id',
        'edited_at',
    ];

    protected $casts = [
        'data' => 'array',
        'edited_at' => 'datetime',
    ];

    /**
     * Get the conversation that owns the message.
     */
    public function conversation(): BelongsTo
    {
        return $this->belongsTo(Conversation::class);
    }

    /**
     * Get the sendable entity (User, etc.).
     */
    public function sendable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Get the message this message is replying to.
     */
    public function replyTo(): BelongsTo
    {
        return $this->belongsTo(Message::class, 'reply_to_id');
    }

    /**
     * Get messages that reply to this message.
     */
    public function replies()
    {
        return $this->hasMany(Message::class, 'reply_to_id');
    }

    /**
     * Scope messages by conversation.
     */
    public function scopeInConversation($query, $conversationId)
    {
        return $query->where('conversation_id', $conversationId);
    }

    /**
     * Scope messages by sender.
     */
    public function scopeBySender($query, $senderType, $senderId)
    {
        return $query->where('sendable_type', $senderType)
                    ->where('sendable_id', $senderId);
    }

    /**
     * Scope messages by type.
     */
    public function scopeOfType($query, $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Check if the message has been edited.
     */
    public function isEdited(): bool
    {
        return $this->edited_at !== null;
    }

    /**
     * Mark the message as edited.
     */
    public function markAsEdited(): void
    {
        $this->update(['edited_at' => now()]);
    }

    /**
     * Get the sender's name.
     */
    public function getSenderNameAttribute(): string
    {
        if ($this->sendable) {
            return $this->sendable->name ?? 'Unknown';
        }

        return 'Unknown';
    }

    /**
     * Get formatted message time.
     */
    public function getFormattedTimeAttribute(): string
    {
        return $this->created_at->format('H:i');
    }

    /**
     * Get formatted message date.
     */
    public function getFormattedDateAttribute(): string
    {
        return $this->created_at->format('Y-m-d');
    }

    /**
     * Check if message is from today.
     */
    public function isFromToday(): bool
    {
        return $this->created_at->isToday();
    }

    /**
     * Get message preview (truncated body).
     */
    public function getPreviewAttribute(): string
    {
        return strlen($this->body) > 50 
            ? substr($this->body, 0, 50) . '...'
            : $this->body;
    }
}
