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
        Schema::create('match_participants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tft_match_id')->constrained()->onDelete('cascade');
            $table->foreignId('player_id')->nullable()->constrained()->onDelete('set null');
            $table->string('puuid');
            $table->integer('placement');
            $table->integer('level');
            $table->integer('gold_left');
            $table->integer('total_damage_to_players');
            $table->boolean('win')->default(false);
            $table->integer('last_round');
            $table->decimal('time_eliminated', 8, 2);
            $table->json('companion')->nullable();
            $table->json('traits');
            $table->json('units');
            $table->json('augments');
            $table->timestamps();
            
            $table->index(['tft_match_id', 'placement']);
            $table->index(['player_id', 'placement']);
            $table->index(['placement', 'win']);
            $table->index('puuid');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('match_participants');
    }
};
