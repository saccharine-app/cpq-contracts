<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('catalog_items', function (Blueprint $table) {
            $table->uuid('id')->primary();
            
            $table->string('sku')->unique()->nullable();
            $table->string('canonical_name');
            $table->string('accounting_code')->nullable();
            $table->string('default_tax_class')->default('STANDARD');
            
            $table->json('attributes')->nullable();
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('catalog_items');
    }
};