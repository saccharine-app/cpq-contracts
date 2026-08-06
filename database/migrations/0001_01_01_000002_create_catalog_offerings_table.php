<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('catalog_offerings', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('catalog_item_id')->constrained()->cascadeOnDelete();
            
            // Polymorphic connection to the host app (e.g., Location, Region)
            $table->uuidMorphs('owner');
            
            $table->string('display_name');
            $table->text('display_description')->nullable();
            $table->string('local_code')->nullable();
            $table->boolean('is_active')->default(true);
            
            $table->timestamps();

            // A specific owner can only have one offering per catalog item
            $table->unique(['owner_type', 'owner_id', 'catalog_item_id'], 'offering_owner_item_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('catalog_offerings');
    }
};