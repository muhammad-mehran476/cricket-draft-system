<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // draft_queue.team_id -> teams.id
        Schema::table('draft_queue', function (Blueprint $table) {
            $table->dropForeign(['team_id']);
            $table->foreign('team_id')
                  ->references('id')->on('teams')
                  ->onDelete('cascade');
        });

        // players.team_id -> teams.id (set null so player isn't deleted, just unassigned)
        Schema::table('players', function (Blueprint $table) {
            $table->dropForeign(['team_id']);
            $table->foreign('team_id')
                  ->references('id')->on('teams')
                  ->onDelete('set null');
        });

        // draft_picks.team_id -> teams.id (if this table exists)
        if (Schema::hasTable('draft_picks')) {
            Schema::table('draft_picks', function (Blueprint $table) {
                $table->dropForeign(['team_id']);
                $table->foreign('team_id')
                      ->references('id')->on('teams')
                      ->onDelete('cascade');
            });
        }

        // draft_picks.player_id -> players.id (if applicable)
        if (Schema::hasTable('draft_picks')) {
            Schema::table('draft_picks', function (Blueprint $table) {
                $table->dropForeign(['player_id']);
                $table->foreign('player_id')
                      ->references('id')->on('players')
                      ->onDelete('cascade');
            });
        }

        // player_stats.player_id -> players.id (if applicable)
        if (Schema::hasTable('player_stats')) {
            Schema::table('player_stats', function (Blueprint $table) {
                $table->dropForeign(['player_id']);
                $table->foreign('player_id')
                      ->references('id')->on('players')
                      ->onDelete('cascade');
            });
        }
    }

    public function down(): void
    {
        Schema::table('draft_queue', function (Blueprint $table) {
            $table->dropForeign(['team_id']);
            $table->foreign('team_id')->references('id')->on('teams');
        });

        Schema::table('players', function (Blueprint $table) {
            $table->dropForeign(['team_id']);
            $table->foreign('team_id')->references('id')->on('teams');
        });
    }
};