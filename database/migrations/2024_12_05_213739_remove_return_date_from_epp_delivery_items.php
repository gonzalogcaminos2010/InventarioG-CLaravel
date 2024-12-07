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
        Schema::table('epp_delivery_items', function (Blueprint $table) {
            $table->dropColumn('estimated_return_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('epp_delivery_items', function (Blueprint $table) {
            $table->date('estimated_return_date')->nullable();
        });
    }
};
