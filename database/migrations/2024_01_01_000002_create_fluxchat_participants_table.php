<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('fluxchat_participants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('conversation_id')->constrained('fluxchat_conversations')->onDelete('cascade');
            $table->morphs('participatable'); // User, Contact, etc.
            $table->string('role')->default('member'); // member, admin, moderator
            $table->timestamp('joined_at')->useCurrent();
            $table->timestamp('last_read_at')->nullable();
            $table->boolean('is_admin')->default(false);
            $table->boolean('is_muted')->default(false);
            $table->timestamps();

            $table->unique(['conversation_id', 'participatable_type', 'participatable_id'], 'fluxchat_participants_unique');
            $table->index(['participatable_type', 'participatable_id']);
            $table->index('last_read_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fluxchat_participants');
    }
};
