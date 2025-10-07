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
        Schema::create('tft_matches', function (Blueprint $table) {
            $table->id();
            $table->string('match_id')->unique();
            $table->timestamp('game_datetime');
            $table->decimal('game_length', 8, 2);
            $table->string('game_variation')->nullable();
            $table->string('game_version');
            $table->integer('queue_id');
            $table->integer('tft_set_number');
            $table->string('data_version');
            $table->timestamps();
            
            $table->index(['game_datetime', 'queue_id']);
            $table->index('tft_set_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tft_matches');
    }
};
