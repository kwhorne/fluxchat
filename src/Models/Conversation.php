<?php

namespace Wirelabs\FluxChat\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Conversation extends Model
{
    use SoftDeletes;

    protected $table = 'fluxchat_conversations';

    protected $fillable = [
        'title',
        'type',
        'is_group',
        'description',
        'avatar',
        'settings',
    ];

    protected $casts = [
        'is_group' => 'boolean',
        'settings' => 'array',
    ];

    /**
     * Get all messages for this conversation.
     */
    public function messages(): HasMany
    {
        return $this->hasMany(Message::class);
    }

    /**
     * Get all participants for this conversation.
     */
    public function participants(): HasMany
    {
        return $this->hasMany(Participant::class);
    }

    /**
     * Get the latest message for this conversation.
     */
    public function latestMessage()
    {
        return $this->hasOne(Message::class)->latest();
    }

    /**
     * Get participants as polymorphic relationships.
     */
    public function users(): MorphToMany
    {
        return $this->morphedByMany(
            config('auth.providers.users.model', \App\Models\User::class),
            'participatable',
            'fluxchat_participants'
        );
    }

    /**
     * Add a participant to the conversation.
     */
    public function addParticipant($participant): void
    {
        $this->participants()->firstOrCreate([
            'participatable_type' => get_class($participant),
            'participatable_id' => $participant->id,
        ]);
    }

    /**
     * Remove a participant from the conversation.
     */
    public function removeParticipant($participant): void
    {
        $this->participants()
            ->where('participatable_type', get_class($participant))
            ->where('participatable_id', $participant->id)
            ->delete();
    }

    /**
     * Check if a user is a participant.
     */
    public function hasParticipant($participant): bool
    {
        return $this->participants()
            ->where('participatable_type', get_class($participant))
            ->where('participatable_id', $participant->id)
            ->exists();
    }

    /**
     * Get unread messages count for a specific participant.
     */
    public function getUnreadCount($participant): int
    {
        $participantModel = $this->participants()
            ->where('participatable_type', get_class($participant))
            ->where('participatable_id', $participant->id)
            ->first();

        if (!$participantModel) {
            return 0;
        }

        return $this->messages()
            ->where('created_at', '>', $participantModel->last_read_at ?? $this->created_at)
            ->count();
    }

    /**
     * Mark conversation as read for a participant.
     */
    public function markAsRead($participant): void
    {
        $this->participants()
            ->where('participatable_type', get_class($participant))
            ->where('participatable_id', $participant->id)
            ->update(['last_read_at' => now()]);
    }
}
