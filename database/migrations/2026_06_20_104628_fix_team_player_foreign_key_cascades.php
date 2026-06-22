<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // draft_picks.player_id -> players.id : cascade (pick is meaningless without the player)
        Schema::table('draft_picks', function (Blueprint $table) {
            $table->dropForeign(['player_id']);
            $table->foreign('player_id')
                  ->references('id')->on('players')
                  ->onDelete('cascade');
        });

        // draft_picks.team_id -> teams.id : cascade (pick is meaningless without the team)
        Schema::table('draft_picks', function (Blueprint $table) {
            $table->dropForeign(['team_id']);
            $table->foreign('team_id')
                  ->references('id')->on('teams')
                  ->onDelete('cascade');
        });

        // draft_queue.team_id -> teams.id : cascade (queue entry is meaningless without the team)
        Schema::table('draft_queue', function (Blueprint $table) {
            $table->dropForeign(['team_id']);
            $table->foreign('team_id')
                  ->references('id')->on('teams')
                  ->onDelete('cascade');
        });

        // draft_sessions.current_team_turn_id -> teams.id : set null (must NOT delete the whole session)
        Schema::table('draft_sessions', function (Blueprint $table) {
            $table->dropForeign(['current_team_turn_id']);
        });
        Schema::table('draft_sessions', function (Blueprint $table) {
            $table->unsignedBigInteger('current_team_turn_id')->nullable()->change();
        });
        Schema::table('draft_sessions', function (Blueprint $table) {
            $table->foreign('current_team_turn_id')
                  ->references('id')->on('teams')
                  ->onDelete('set null');
        });

        // matches.home_team_id -> teams.id : set null (preserve match history)
        Schema::table('matches', function (Blueprint $table) {
            $table->dropForeign(['home_team_id']);
        });
        Schema::table('matches', function (Blueprint $table) {
            $table->unsignedBigInteger('home_team_id')->nullable()->change();
        });
        Schema::table('matches', function (Blueprint $table) {
            $table->foreign('home_team_id')
                  ->references('id')->on('teams')
                  ->onDelete('set null');
        });

        // matches.away_team_id -> teams.id : set null (preserve match history)
        Schema::table('matches', function (Blueprint $table) {
            $table->dropForeign(['away_team_id']);
        });
        Schema::table('matches', function (Blueprint $table) {
            $table->unsignedBigInteger('away_team_id')->nullable()->change();
        });
        Schema::table('matches', function (Blueprint $table) {
            $table->foreign('away_team_id')
                  ->references('id')->on('teams')
                  ->onDelete('set null');
        });

        // players.team_id -> teams.id : set null (deleting a team should NOT delete its players)
        Schema::table('players', function (Blueprint $table) {
            $table->dropForeign(['team_id']);
        });
        Schema::table('players', function (Blueprint $table) {
            $table->unsignedBigInteger('team_id')->nullable()->change();
        });
        Schema::table('players', function (Blueprint $table) {
            $table->foreign('team_id')
                  ->references('id')->on('teams')
                  ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('draft_picks', function (Blueprint $table) {
            $table->dropForeign(['player_id']);
            $table->foreign('player_id')->references('id')->on('players');
        });

        Schema::table('draft_picks', function (Blueprint $table) {
            $table->dropForeign(['team_id']);
            $table->foreign('team_id')->references('id')->on('teams');
        });

        Schema::table('draft_queue', function (Blueprint $table) {
            $table->dropForeign(['team_id']);
            $table->foreign('team_id')->references('id')->on('teams');
        });

        Schema::table('draft_sessions', function (Blueprint $table) {
            $table->dropForeign(['current_team_turn_id']);
            $table->foreign('current_team_turn_id')->references('id')->on('teams');
        });

        Schema::table('matches', function (Blueprint $table) {
            $table->dropForeign(['home_team_id']);
            $table->foreign('home_team_id')->references('id')->on('teams');
        });

        Schema::table('matches', function (Blueprint $table) {
            $table->dropForeign(['away_team_id']);
            $table->foreign('away_team_id')->references('id')->on('teams');
        });

        Schema::table('players', function (Blueprint $table) {
            $table->dropForeign(['team_id']);
            $table->foreign('team_id')->references('id')->on('teams');
        });
    }
};