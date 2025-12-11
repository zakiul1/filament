<?php

// database/migrations/2025_12_10_000001_create_sample_invoice_items_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('sample_invoice_items', function (Blueprint $table) {
            $table->id();

            $table->foreignId('sample_invoice_id')->constrained()->cascadeOnDelete();

            $table->integer('line_no')->nullable();
            $table->string('style_ref')->nullable();
            $table->string('item_description');
            $table->string('color')->nullable();
            $table->string('size')->nullable();

            $table->foreignId('factory_subcategory_id')->nullable()->constrained()->nullOnDelete();

            $table->string('unit')->default('PCS');
            $table->decimal('quantity', 15, 3)->default(0);
            $table->decimal('unit_price', 15, 4)->default(0); // often free / nominal
            $table->decimal('amount', 15, 2)->default(0);

            $table->string('sample_type')->nullable(); // e.g. FIT, SIZE SET, PP, etc.

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sample_invoice_items');
    }
};