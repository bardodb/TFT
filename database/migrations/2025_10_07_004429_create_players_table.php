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
        Schema::create('players', function (Blueprint $table) {
            $table->id();
            $table->string('puuid')->unique();
            $table->string('summoner_id')->nullable();
            $table->string('game_name')->nullable();
            $table->string('tag_line')->nullable();
            $table->string('tier')->nullable();
            $table->string('rank')->nullable();
            $table->integer('league_points')->default(0);
            $table->integer('wins')->default(0);
            $table->integer('losses')->default(0);
            $table->string('region');
            $table->timestamps();
            
            $table->index(['tier', 'rank']);
            $table->index('region');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('players');
    }
};
