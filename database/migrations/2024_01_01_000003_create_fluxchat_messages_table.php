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
        Schema::create('fluxchat_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('conversation_id')->constrained('fluxchat_conversations')->onDelete('cascade');
            $table->morphs('sendable'); // User, etc.
            $table->text('body');
            $table->string('type')->default('text'); // text, image, file, system, etc.
            $table->json('data')->nullable(); // For storing additional data (file info, etc.)
            $table->foreignId('reply_to_id')->nullable()->constrained('fluxchat_messages')->onDelete('set null');
            $table->timestamp('edited_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['conversation_id', 'created_at']);
            $table->index(['sendable_type', 'sendable_id']);
            $table->index('type');
            $table->index('reply_to_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fluxchat_messages');
    }
};
