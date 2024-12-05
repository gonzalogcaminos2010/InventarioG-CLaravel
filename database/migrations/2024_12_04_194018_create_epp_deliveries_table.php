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
        Schema::create('epp_deliveries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained();
            $table->foreignId('user_id')->constrained();
            $table->foreignId('warehouse_id')->constrained();
            $table->date('delivery_date');
            $table->enum('status', ['pending', 'delivered', 'returned'])->default('pending');
            $table->text('comments')->nullable();
        $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('epp_deliveries');
    }
};
