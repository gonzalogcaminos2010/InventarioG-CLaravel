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
        Schema::table('epp_deliveries', function (Blueprint $table) {
            $table->foreignId('employee_id')->constrained();
            $table->foreignId('user_id')->constrained();
            $table->foreignId('warehouse_id')->constrained();
            $table->date('delivery_date');
            $table->enum('status', ['completed'])->default('completed');
            $table->text('comments')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('epp_deliveries', function (Blueprint $table) {
            $table->dropForeign(['employee_id']);
            $table->dropForeign(['user_id']);
            $table->dropForeign(['warehouse_id']);
            $table->dropColumn([
                'employee_id',
                'user_id',
                'warehouse_id',
                'delivery_date',
                'status',
                'comments'
            ]);
        });

    }
};
