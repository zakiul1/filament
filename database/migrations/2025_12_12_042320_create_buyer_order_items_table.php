<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('buyer_order_items', function (Blueprint $table) {
            $table->id();

            $table->foreignId('buyer_order_id')->constrained('buyer_orders')->cascadeOnDelete();

            $table->unsignedInteger('line_no')->default(1);

            $table->string('style_ref', 80)->nullable();
            $table->string('item_description', 255);
            $table->string('color', 60)->nullable();
            $table->string('size', 60)->nullable();
            $table->string('unit', 20)->default('PCS');

            // Category from your factory master subcategory
            $table->foreignId('factory_subcategory_id')->nullable()->constrained('factory_subcategories')->nullOnDelete();

            // Default factory for the line (optional)
            $table->foreignId('factory_id')->nullable()->constrained('factories')->nullOnDelete();

            $table->decimal('order_qty', 16, 4)->default(0);
            $table->decimal('unit_price', 16, 4)->default(0);
            $table->decimal('amount', 16, 4)->default(0);

            $table->text('remarks')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('buyer_order_items');
    }
};