<?php

// database/migrations/2025_12_10_000000_create_sample_invoices_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('sample_invoices', function (Blueprint $table) {
            $table->id();

            $table->string('sample_number')->unique();
            $table->date('sample_date')->nullable();
            $table->string('status')->default('draft'); // draft, sent, approved, cancelled

            // Parties
            $table->foreignId('customer_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('beneficiary_company_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('currency_id')->nullable()->constrained()->nullOnDelete();

            // Shipment / Terms
            $table->foreignId('incoterm_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('shipment_mode_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('port_of_loading_id')->nullable()->constrained('ports')->nullOnDelete();
            $table->foreignId('port_of_discharge_id')->nullable()->constrained('ports')->nullOnDelete();

            // Courier info (for sending samples)
            $table->foreignId('courier_id')->nullable()->constrained()->nullOnDelete();
            $table->string('courier_tracking_no')->nullable();

            // Amounts
            $table->decimal('subtotal', 15, 2)->default(0);
            $table->decimal('discount_amount', 15, 2)->default(0);
            $table->decimal('other_charges', 15, 2)->default(0);
            $table->decimal('total_amount', 15, 2)->default(0);
            $table->string('total_amount_in_words')->nullable();

            // Remarks
            $table->text('remarks')->nullable();
            $table->text('internal_notes')->nullable();

            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sample_invoices');
    }
};