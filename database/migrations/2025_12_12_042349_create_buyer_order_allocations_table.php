<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('buyer_order_allocations', function (Blueprint $table) {
            $table->id();

            $table->foreignId('buyer_order_item_id')->constrained('buyer_order_items')->cascadeOnDelete();
            $table->foreignId('factory_id')->constrained('factories')->cascadeOnDelete();

            $table->decimal('allocated_qty', 16, 4)->default(0);
            $table->text('notes')->nullable();

            $table->timestamps();

            $table->unique(['buyer_order_item_id', 'factory_id'], 'uniq_item_factory_allocation');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('buyer_order_allocations');
    }
};