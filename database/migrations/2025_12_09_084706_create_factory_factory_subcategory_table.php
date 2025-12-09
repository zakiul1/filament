<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('factory_factory_subcategory', function (Blueprint $table) {
            $table->id();

            $table->foreignId('factory_id')
                ->constrained('factories')
                ->cascadeOnDelete();

            $table->foreignId('factory_subcategory_id')
                ->constrained('factory_subcategories')
                ->cascadeOnDelete();

            $table->timestamps();

            $table->unique(['factory_id', 'factory_subcategory_id'], 'factory_subcategory_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('factory_factory_subcategory');
    }
};