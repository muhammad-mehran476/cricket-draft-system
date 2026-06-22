<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('players', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('email');
            $table->string('phone', 20);
            $table->text('address');
            $table->string('city', 100);
            $table->string('profile_picture')->nullable();
            $table->enum('role', ['batsman', 'bowler', 'all_rounder', 'wicket_keeper']);
            $table->enum('skill_level', ['good', 'better', 'best'])->default('good');
            $table->enum('bowling_type', ['fast', 'medium', 'spin', 'none'])->default('none');
            $table->enum('batting_style', ['right_hand', 'left_hand'])->default('right_hand');
            $table->foreignId('category_id')->nullable()->constrained()->nullOnDelete();
            $table->string('payment_slip')->nullable();
            $table->enum('status', ['pending', 'approved', 'rejected', 'drafted'])->default('pending');
            $table->text('rejection_reason')->nullable();
            $table->foreignId('team_id')->nullable()->constrained()->nullOnDelete();
            $table->boolean('rules_accepted')->default(false);
            $table->timestamps();

            $table->index('status');
            $table->index(['category_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('players');
    }
};
