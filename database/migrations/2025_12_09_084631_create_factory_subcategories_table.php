<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('factory_subcategories', function (Blueprint $table) {
            $table->id();

            $table->foreignId('factory_category_id')
                ->constrained('factory_categories')
                ->cascadeOnDelete();

            $table->string('name');               // e.g. Knit Top, Knit Bottom, Denim, Outerwear
            $table->string('slug')->nullable();   // knit-top, denim
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('factory_subcategories');
    }
};