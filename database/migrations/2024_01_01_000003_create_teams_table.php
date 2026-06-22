<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('teams', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('team_name');
            $table->string('captain_name');
            $table->string('email');
            $table->string('phone', 20);
            $table->text('address');
            $table->string('team_logo')->nullable();
            $table->string('captain_image')->nullable();
            $table->string('team_banner')->nullable();
            $table->string('payment_slip')->nullable();
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->text('rejection_reason')->nullable();
            $table->integer('draft_order')->nullable();
            $table->integer('total_players_drafted')->default(0);
            $table->timestamps();

            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('teams');
    }
};
