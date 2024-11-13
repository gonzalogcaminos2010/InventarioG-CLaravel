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
        Schema::create('movements', function (Blueprint $table) {
        $table->id();
        $table->foreignId('item_id')->constrained();
        $table->foreignId('user_id')->constrained();
        $table->foreignId('source_warehouse_id')->nullable()->constrained('warehouses');
        $table->foreignId('destination_warehouse_id')->nullable()->constrained('warehouses');
        $table->enum('type', ['entry', 'exit', 'transfer']);
        $table->enum('status', ['pending', 'completed', 'cancelled']);
        $table->integer('quantity');
        $table->text('comments')->nullable();
        $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('movements');
    }
};
