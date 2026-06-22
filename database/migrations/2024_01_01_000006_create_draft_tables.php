<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('draft_rounds', function (Blueprint $table) {
            $table->id();
            $table->foreignId('draft_session_id')->constrained()->cascadeOnDelete();
            $table->foreignId('category_id')->constrained();
            $table->integer('round_number');
            $table->json('team_order')->comment('Array of team IDs in randomized pick order');
            $table->enum('status', ['pending', 'active', 'completed'])->default('pending');
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            $table->index(['draft_session_id', 'status']);
        });

        Schema::create('draft_picks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('draft_session_id')->constrained()->cascadeOnDelete();
            $table->foreignId('draft_round_id')->constrained()->cascadeOnDelete();
            $table->foreignId('team_id')->constrained();
            $table->foreignId('player_id')->constrained()->unique();
            $table->foreignId('category_id')->constrained();
            $table->integer('pick_number');
            $table->integer('time_taken_seconds')->nullable();
            $table->boolean('is_auto_pick')->default(false);
            $table->timestamp('picked_at');
            $table->timestamps();

            $table->index(['draft_session_id', 'team_id']);
        });

        Schema::create('draft_queue', function (Blueprint $table) {
            $table->id();
            $table->foreignId('draft_session_id')->constrained()->cascadeOnDelete();
            $table->foreignId('draft_round_id')->constrained()->cascadeOnDelete();
            $table->foreignId('team_id')->constrained();
            $table->integer('pick_position');
            $table->enum('status', ['waiting', 'active', 'done', 'skipped'])->default('waiting');
            $table->timestamp('timer_expires_at')->nullable();
            $table->timestamps();

            $table->index(['draft_session_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('draft_queue');
        Schema::dropIfExists('draft_picks');
        Schema::dropIfExists('draft_rounds');
    }
};
