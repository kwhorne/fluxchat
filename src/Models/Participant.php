<?php

namespace Wirelabs\FluxChat\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Participant extends Model
{
    protected $table = 'fluxchat_participants';

    protected $fillable = [
        'conversation_id',
        'participatable_type',
        'participatable_id',
        'role',
        'joined_at',
        'last_read_at',
        'is_admin',
        'is_muted',
    ];

    protected $casts = [
        'joined_at' => 'datetime',
        'last_read_at' => 'datetime',
        'is_admin' => 'boolean',
        'is_muted' => 'boolean',
    ];

    /**
     * Get the conversation that owns the participant.
     */
    public function conversation(): BelongsTo
    {
        return $this->belongsTo(Conversation::class);
    }

    /**
     * Get the participatable entity (User, etc.).
     */
    public function participatable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Scope participants by conversation.
     */
    public function scopeInConversation($query, $conversationId)
    {
        return $query->where('conversation_id', $conversationId);
    }

    /**
     * Scope admin participants.
     */
    public function scopeAdmins($query)
    {
        return $query->where('is_admin', true);
    }

    /**
     * Scope non-muted participants.
     */
    public function scopeNotMuted($query)
    {
        return $query->where('is_muted', false);
    }

    /**
     * Check if participant is an admin.
     */
    public function isAdmin(): bool
    {
        return $this->is_admin;
    }

    /**
     * Check if participant is muted.
     */
    public function isMuted(): bool
    {
        return $this->is_muted;
    }

    /**
     * Mark participant as having read the conversation.
     */
    public function markAsRead(): void
    {
        $this->update(['last_read_at' => now()]);
    }

    /**
     * Get unread messages count for this participant.
     */
    public function getUnreadCount(): int
    {
        return $this->conversation
            ->messages()
            ->where('created_at', '>', $this->last_read_at ?? $this->joined_at)
            ->count();
    }

    /**
     * Promote participant to admin.
     */
    public function promoteToAdmin(): void
    {
        $this->update(['is_admin' => true]);
    }

    /**
     * Demote participant from admin.
     */
    public function demoteFromAdmin(): void
    {
        $this->update(['is_admin' => false]);
    }

    /**
     * Mute participant.
     */
    public function mute(): void
    {
        $this->update(['is_muted' => true]);
    }

    /**
     * Unmute participant.
     */
    public function unmute(): void
    {
        $this->update(['is_muted' => false]);
    }

    /**
     * Get participant's display name.
     */
    public function getDisplayNameAttribute(): string
    {
        if ($this->participatable) {
            return $this->participatable->name ?? 'Unknown';
        }

        return 'Unknown';
    }
}
