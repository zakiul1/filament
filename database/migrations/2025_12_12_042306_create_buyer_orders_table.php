<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('buyer_orders', function (Blueprint $table) {
            $table->id();

            $table->string('order_number', 80)->unique();
            $table->date('order_date')->nullable();

            // Links to existing documents (optional)
            $table->foreignId('proforma_invoice_id')->nullable()->constrained('proforma_invoices')->nullOnDelete();
            $table->foreignId('lc_receive_id')->nullable()->constrained('lc_receives')->nullOnDelete();
            $table->foreignId('commercial_invoice_id')->nullable()->constrained('commercial_invoices')->nullOnDelete();

            // Parties
            $table->foreignId('customer_id')->constrained('customers')->cascadeOnDelete();
            $table->foreignId('beneficiary_company_id')->nullable()->constrained('beneficiary_companies')->nullOnDelete();

            // Planning fields
            $table->string('buyer_po_number', 120)->nullable();
            $table->string('season', 50)->nullable();        // e.g. SS26
            $table->string('department', 80)->nullable();    // e.g. Mens, Ladies, Kids
            $table->string('merchandiser_name', 120)->nullable();

            $table->date('shipment_date_from')->nullable();
            $table->date('shipment_date_to')->nullable();

            $table->decimal('order_value', 16, 4)->default(0);
            $table->string('status', 30)->default('draft'); // draft, running, shipped, closed, cancelled

            $table->text('remarks')->nullable();
            $table->text('internal_notes')->nullable();

            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('buyer_orders');
    }
};