<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('buyer_order_item_allocations', function (Blueprint $table) {
            $table->id();

            $table->foreignId('buyer_order_item_id')
                ->constrained('buyer_order_items')
                ->cascadeOnDelete();

            $table->foreignId('factory_id')
                ->nullable()
                ->constrained('factories')
                ->nullOnDelete();

            $table->decimal('qty', 14, 4)->default(0);

            $table->text('remarks')->nullable();

            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamps();

            $table->index(['buyer_order_item_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('buyer_order_item_allocations');
    }
};