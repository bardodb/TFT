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
        Schema::create('champions', function (Blueprint $table) {
            $table->id();
            $table->string('champion_id')->unique();
            $table->string('name');
            $table->integer('cost');
            $table->json('traits');
            $table->json('stats');
            $table->json('ability');
            $table->string('image_url')->nullable();
            $table->timestamps();
            
            $table->index('cost');
            $table->index('name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('champions');
    }
};
