<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('offering_prices', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('catalog_offering_id')->constrained()->cascadeOnDelete();
            
            $table->integer('price_cents');
            $table->string('currency', 3)->default('CAD');
            $table->boolean('is_taxable_override')->nullable();
            
            // Temporal Validity
            $table->timestamp('effective_at');
            $table->timestamp('ends_at')->nullable();
            
            $table->json('rules_manifest')->nullable();
            
            $table->timestamps();

            // Critical index for point-in-time pricing lookups
            $table->index(['catalog_offering_id', 'effective_at', 'ends_at'], 'offering_prices_temporal_index');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('offering_prices');
    }
};