<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('lc_amendments', function (Blueprint $table) {
            $table->id();

            $table->foreignId('lc_receive_id')
                ->constrained('lc_receives')
                ->cascadeOnDelete();

            $table->string('amendment_number', 50);
            $table->date('amendment_date')->nullable();

            // Type of amendment
            $table->string('amendment_type', 50)->nullable(); // VALUE_INCREASE, DATE_EXTEND, etc.

            // Amount-related changes
            $table->decimal('previous_lc_amount', 18, 2)->nullable();
            $table->decimal('new_lc_amount', 18, 2)->nullable();

            $table->decimal('previous_tolerance_plus', 5, 2)->nullable();
            $table->decimal('new_tolerance_plus', 5, 2)->nullable();

            $table->decimal('previous_tolerance_minus', 5, 2)->nullable();
            $table->decimal('new_tolerance_minus', 5, 2)->nullable();

            // Date-related changes
            $table->date('previous_expiry_date')->nullable();
            $table->date('new_expiry_date')->nullable();

            $table->date('previous_last_shipment_date')->nullable();
            $table->date('new_last_shipment_date')->nullable();

            // Generic details
            $table->text('change_summary')->nullable();   // human text summary
            $table->text('other_changes')->nullable();
            $table->text('remarks')->nullable();
            $table->text('internal_notes')->nullable();

            $table->string('status', 20)->default('draft'); // draft / confirmed / cancelled

            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lc_amendments');
    }
};