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
        Schema::create('items', function (Blueprint $table) {
            $table->id();
            $table->string('item_id')->unique();
            $table->string('name');
            $table->text('description');
            $table->string('icon_url')->nullable();
            $table->json('from_items')->nullable();
            $table->json('into_items')->nullable();
            $table->integer('gold_cost')->default(0);
            $table->string('category');
            $table->decimal('pick_rate', 5, 2)->default(0);
            $table->decimal('win_rate', 5, 2)->default(0);
            $table->decimal('avg_placement', 5, 2)->default(0);
            $table->timestamps();
            
            $table->index('category');
            $table->index('gold_cost');
            $table->index('name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('items');
    }
};
