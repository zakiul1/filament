<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('proforma_invoices', function (Blueprint $table) {
            $table->id();

            $table->string('pi_number')->unique();     // e.g. PI-2025-0001
            $table->date('pi_date');
            $table->unsignedInteger('revision_no')->nullable();

            // Parties & banks
            $table->foreignId('customer_id')->constrained()->cascadeOnUpdate();
            $table->foreignId('beneficiary_company_id')
                ->nullable()
                ->constrained('beneficiary_companies')
                ->nullOnDelete();
            $table->foreignId('beneficiary_bank_account_id')
                ->nullable()
                ->constrained('beneficiary_bank_accounts')
                ->nullOnDelete();
            $table->foreignId('customer_bank_id')
                ->nullable()
                ->constrained('customer_banks')
                ->nullOnDelete();

            // Terms & shipment
            $table->foreignId('currency_id')->constrained('currencies');
            $table->foreignId('incoterm_id')->nullable()->constrained('incoterms')->nullOnDelete();
            $table->foreignId('shipment_mode_id')->nullable()->constrained('shipment_modes')->nullOnDelete();
            $table->foreignId('port_of_loading_id')->nullable()->constrained('ports')->nullOnDelete();
            $table->foreignId('port_of_discharge_id')->nullable()->constrained('ports')->nullOnDelete();
            $table->foreignId('payment_term_id')->nullable()->constrained('payment_terms')->nullOnDelete();
            $table->foreignId('courier_id')->nullable()->constrained('couriers')->nullOnDelete();

            $table->string('buyer_reference')->nullable();     // buyer PO / style ref
            $table->string('place_of_delivery')->nullable();
            $table->integer('shipment_lead_time_days')->nullable();
            $table->date('shipment_date_from')->nullable();
            $table->date('shipment_date_to')->nullable();
            $table->date('validity_date')->nullable();          // PI validity

            // Amounts
            $table->decimal('subtotal', 15, 2)->default(0);
            $table->decimal('discount_amount', 15, 2)->default(0);
            $table->decimal('other_charges', 15, 2)->default(0);
            $table->decimal('total_amount', 15, 2)->default(0);
            $table->string('total_amount_in_words')->nullable();

            $table->string('status')->default('draft');        // draft, sent, accepted, cancelled

            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();

            $table->text('remarks')->nullable();
            $table->text('internal_notes')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('proforma_invoices');
    }
};