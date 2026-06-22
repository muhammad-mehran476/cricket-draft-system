<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('draft_sessions', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->enum('status', ['pending', 'active', 'paused', 'completed'])->default('pending');
            $table->foreignId('current_category_id')->nullable()->constrained('categories')->nullOnDelete();
            $table->integer('current_round')->default(1);
            $table->foreignId('current_team_turn_id')->nullable()->constrained('teams')->nullOnDelete();
            $table->integer('timer_seconds')->default(300);
            $table->timestamp('timer_started_at')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('draft_sessions');
    }
};
