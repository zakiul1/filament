<?php
// database/migrations/2025_12_10_000002_create_commercial_invoice_items_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('commercial_invoice_items', function (Blueprint $table) {
            $table->id();

            $table->foreignId('commercial_invoice_id')
                ->constrained()->cascadeOnDelete();

            $table->unsignedInteger('line_no')->default(1);
            $table->foreignId('proforma_invoice_item_id')->nullable()
                ->constrained()->nullOnDelete();

            $table->string('style_ref', 100)->nullable();
            $table->string('item_description', 500);
            $table->string('hs_code', 50)->nullable();

            $table->foreignId('factory_subcategory_id')->nullable()
                ->constrained()->nullOnDelete();

            $table->string('color', 100)->nullable();
            $table->string('size', 100)->nullable();
            $table->string('unit', 20)->default('PCS');

            $table->decimal('quantity', 18, 4)->default(0);
            $table->decimal('unit_price', 18, 4)->default(0);
            $table->decimal('amount', 18, 4)->default(0);

            $table->unsignedInteger('carton_count')->nullable();
            $table->decimal('net_weight', 18, 4)->nullable();
            $table->decimal('gross_weight', 18, 4)->nullable();
            $table->decimal('cbm', 18, 4)->nullable();

            $table->string('remarks', 500)->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('commercial_invoice_items');
    }
};