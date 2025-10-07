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
        Schema::create('augments', function (Blueprint $table) {
            $table->id();
            $table->string('augment_id')->unique();
            $table->string('name');
            $table->text('description');
            $table->string('icon_url')->nullable();
            $table->integer('tier');
            $table->string('category');
            $table->decimal('pick_rate', 5, 2)->default(0);
            $table->decimal('win_rate', 5, 2)->default(0);
            $table->decimal('avg_placement', 5, 2)->default(0);
            $table->timestamps();
            
            $table->index('tier');
            $table->index('category');
            $table->index(['tier', 'category']);
            $table->index('name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('augments');
    }
};
