<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sizes', function (Blueprint $table) {
            $table->id();
            $table->string('name');  // XS, S, M, L, XL, XXL o 38, 40, 42, etc.
            $table->string('description')->nullable();
            $table->timestamps();
        });

        // Modificar la tabla items para agregar los campos de EPP
        Schema::table('items', function (Blueprint $table) {
            $table->foreignId('size_id')->nullable()->constrained();
            $table->boolean('is_epp')->default(false);
            $table->boolean('requires_return')->default(false);
        });
    }

    public function down(): void
    {
        Schema::table('items', function (Blueprint $table) {
            $table->dropForeign(['size_id']);
            $table->dropColumn(['size_id', 'is_epp', 'requires_return']);
        });
        
        Schema::dropIfExists('sizes');
    }
};