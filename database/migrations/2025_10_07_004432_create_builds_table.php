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
        Schema::create('builds', function (Blueprint $table) {
            $table->id();
            $table->foreignId('player_id')->constrained()->onDelete('cascade');
            $table->foreignId('tft_match_id')->constrained()->onDelete('cascade');
            $table->integer('placement');
            $table->integer('level');
            $table->integer('gold_left');
            $table->integer('total_damage_to_players');
            $table->boolean('win')->default(false);
            $table->string('composition_name')->nullable();
            $table->text('description')->nullable();
            $table->decimal('win_rate', 5, 2)->default(0);
            $table->decimal('pick_rate', 5, 2)->default(0);
            $table->decimal('avg_placement', 5, 2)->default(0);
            $table->timestamps();
            
            $table->index(['placement', 'win']);
            $table->index('composition_name');
            $table->index(['player_id', 'placement']);
            $table->index(['win_rate', 'pick_rate']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('builds');
    }
};
