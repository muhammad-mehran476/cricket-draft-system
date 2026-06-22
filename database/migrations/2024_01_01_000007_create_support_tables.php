<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('matches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('home_team_id')->constrained('teams')->cascadeOnDelete();
            $table->foreignId('away_team_id')->nullable()->constrained('teams')->nullOnDelete();
            $table->string('opponent_name')->nullable();
            $table->date('match_date');
            $table->string('venue')->nullable();
            $table->enum('match_type', ['league', 'knockout', 'friendly', 'final'])->default('league');
            $table->integer('home_runs')->nullable();
            $table->integer('home_wickets')->nullable();
            $table->decimal('home_overs', 4, 1)->nullable();
            $table->integer('away_runs')->nullable();
            $table->integer('away_wickets')->nullable();
            $table->decimal('away_overs', 4, 1)->nullable();
            $table->enum('result', ['win', 'loss', 'draw', 'no_result'])->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();

            $table->index('match_date');
        });

        Schema::create('player_stats', function (Blueprint $table) {
            $table->id();
            $table->foreignId('player_id')->constrained()->cascadeOnDelete();
            $table->foreignId('match_id')->nullable()->constrained('matches')->nullOnDelete();
            $table->integer('runs_scored')->default(0);
            $table->integer('balls_faced')->default(0);
            $table->integer('fours')->default(0);
            $table->integer('sixes')->default(0);
            $table->integer('wickets_taken')->default(0);
            $table->decimal('overs_bowled', 4, 1)->default(0.0);
            $table->integer('runs_conceded')->default(0);
            $table->integer('catches')->default(0);
            $table->integer('run_outs')->default(0);
            $table->integer('stumpings')->default(0);
            $table->boolean('is_not_out')->default(false);
            $table->timestamps();

            $table->index('player_id');
        });

        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('action');
            $table->string('model_type')->nullable();
            $table->unsignedBigInteger('model_id')->nullable();
            $table->json('old_values')->nullable();
            $table->json('new_values')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->text('description')->nullable();
            $table->timestamps();

            $table->index('action');
            $table->index('created_at');
            $table->index(['model_type', 'model_id']);
        });

        Schema::create('notifications', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('type');
            $table->morphs('notifiable');
            $table->text('data');
            $table->timestamp('read_at')->nullable();
            $table->timestamps();
        });

        Schema::create('cache', function (Blueprint $table) {
            $table->string('key')->primary();
            $table->mediumText('value');
            $table->integer('expiration');
        });

        Schema::create('jobs', function (Blueprint $table) {
            $table->id();
            $table->string('queue')->index();
            $table->longText('payload');
            $table->unsignedTinyInteger('attempts');
            $table->unsignedInteger('reserved_at')->nullable();
            $table->unsignedInteger('available_at');
            $table->unsignedInteger('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jobs');
        Schema::dropIfExists('cache');
        Schema::dropIfExists('notifications');
        Schema::dropIfExists('audit_logs');
        Schema::dropIfExists('player_stats');
        Schema::dropIfExists('matches');
    }
};
