<?php

namespace Wirelabs\FluxChat\Tests\Unit\Models;

use Wirelabs\FluxChat\Models\Conversation;
use Wirelabs\FluxChat\Models\Participant;
use Wirelabs\FluxChat\Tests\Support\TestCase;
use Wirelabs\FluxChat\Tests\Support\User;

class ParticipantTest extends TestCase
{
    public function test_participant_can_be_created()
    {
        $conversation = Conversation::create([
            'type' => 'private',
            'is_group' => false,
        ]);

        $user = User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
        ]);

        $participant = Participant::create([
            'conversation_id' => $conversation->id,
            'participatable_id' => $user->id,
            'participatable_type' => User::class,
        ]);

        $this->assertInstanceOf(Participant::class, $participant);
        $this->assertEquals($conversation->id, $participant->conversation_id);
        $this->assertEquals($user->id, $participant->participatable_id);
        $this->assertEquals(User::class, $participant->participatable_type);
    }

    public function test_participant_belongs_to_conversation()
    {
        $conversation = Conversation::create([
            'type' => 'private',
            'is_group' => false,
        ]);

        $user = User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
        ]);

        $participant = $conversation->participants()->create([
            'participatable_id' => $user->id,
            'participatable_type' => User::class,
        ]);

        $this->assertEquals($conversation->id, $participant->conversation->id);
    }

    public function test_participant_has_participatable_relationship()
    {
        $conversation = Conversation::create([
            'type' => 'private',
            'is_group' => false,
        ]);

        $user = User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
        ]);

        $participant = $conversation->participants()->create([
            'participatable_id' => $user->id,
            'participatable_type' => User::class,
        ]);

        $this->assertEquals($user->id, $participant->participatable->id);
        $this->assertEquals($user->name, $participant->participatable->name);
    }

    public function test_participant_updates_last_read_at()
    {
        $conversation = Conversation::create([
            'type' => 'private',
            'is_group' => false,
        ]);

        $user = User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
        ]);

        $participant = $conversation->participants()->create([
            'participatable_id' => $user->id,
            'participatable_type' => User::class,
        ]);

        $this->assertNull($participant->last_read_at);

        $participant->update(['last_read_at' => now()]);
        $this->assertNotNull($participant->fresh()->last_read_at);
    }

    public function test_participant_casts_timestamps_correctly()
    {
        $conversation = Conversation::create([
            'type' => 'private',
            'is_group' => false,
        ]);

        $user = User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
        ]);

        $participant = $conversation->participants()->create([
            'participatable_id' => $user->id,
            'participatable_type' => User::class,
            'last_read_at' => now(),
        ]);

        $this->assertInstanceOf(\Illuminate\Support\Carbon::class, $participant->last_read_at);
    }
}