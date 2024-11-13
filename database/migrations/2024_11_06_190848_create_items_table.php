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
        $table->string('part_number')->unique();
        $table->string('name');
        $table->string('description')->nullable();
        $table->foreignId('category_id')->constrained();
        $table->foreignId('brand_id')->constrained();
        $table->integer('minimum_stock')->default(0);
        $table->timestamps();
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