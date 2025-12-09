<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('proforma_invoice_items', function (Blueprint $table) {
            $table->id();

            $table->foreignId('proforma_invoice_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->unsignedInteger('line_no')->nullable();

            $table->string('style_ref')->nullable();           // buyer style
            $table->string('item_description');                // article / description
            $table->string('hs_code')->nullable();

            $table->foreignId('factory_subcategory_id')
                ->nullable()
                ->constrained('factory_subcategories')
                ->nullOnDelete();

            $table->string('color')->nullable();
            $table->string('size')->nullable();

            $table->string('unit')->default('PCS');
            $table->decimal('order_qty', 15, 2)->default(0);
            $table->decimal('unit_price', 15, 4)->default(0);
            $table->decimal('amount', 15, 2)->default(0);      // qty * price

            $table->text('remarks')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('proforma_invoice_items');
    }
};