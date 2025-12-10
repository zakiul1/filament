<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('lc_transfers', function (Blueprint $table) {
            $table->id();

            // Source LC
            $table->foreignId('lc_receive_id')
                ->constrained('lc_receives')
                ->cascadeOnDelete();

            // Destination factory (supplier)
            $table->foreignId('factory_id')
                ->nullable()
                ->constrained('factories')
                ->nullOnDelete();

            // Basic header
            $table->string('transfer_no', 100)->unique();
            $table->date('transfer_date')->nullable();

            // Amount
            $table->foreignId('currency_id')
                ->nullable()
                ->constrained('currencies')
                ->nullOnDelete();

            $table->decimal('transfer_amount', 18, 2)->default(0);
            $table->decimal('tolerance_plus', 5, 2)->nullable();
            $table->decimal('tolerance_minus', 5, 2)->nullable();

            // Status & notes
            $table->string('status', 50)->default('draft');
            $table->text('remarks')->nullable();
            $table->text('internal_notes')->nullable();

            // Audit
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lc_transfers');
    }
};