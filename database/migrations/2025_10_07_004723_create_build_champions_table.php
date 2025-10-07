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
        Schema::create('build_champions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('build_id')->constrained()->onDelete('cascade');
            $table->foreignId('champion_id')->constrained()->onDelete('cascade');
            $table->integer('star_level')->default(1);
            $table->integer('position')->nullable();
            $table->timestamps();
            
            $table->unique(['build_id', 'champion_id']);
            $table->index(['build_id', 'star_level']);
            $table->index('champion_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('build_champions');
    }
};
