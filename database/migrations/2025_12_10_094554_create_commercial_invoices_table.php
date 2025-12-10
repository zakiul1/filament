<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('commercial_invoices', function (Blueprint $table) {
            $table->id();

            $table->string('invoice_number', 100)->unique();
            $table->date('invoice_date');

            $table->foreignId('customer_id')->constrained()->cascadeOnUpdate();
            $table->foreignId('beneficiary_company_id')->constrained()->cascadeOnUpdate();
            $table->foreignId('currency_id')->constrained()->cascadeOnUpdate();

            $table->foreignId('proforma_invoice_id')->nullable()
                ->constrained()->nullOnDelete();
            $table->foreignId('lc_receive_id')->nullable()
                ->constrained('lc_receives')->nullOnDelete();

            $table->foreignId('shipment_mode_id')->nullable()
                ->constrained()->nullOnDelete();
            $table->foreignId('incoterm_id')->nullable()
                ->constrained()->nullOnDelete();
            $table->foreignId('payment_term_id')->nullable()
                ->constrained()->nullOnDelete();

            $table->foreignId('port_of_loading_id')->nullable()
                ->constrained('ports')->nullOnDelete();
            $table->foreignId('port_of_discharge_id')->nullable()
                ->constrained('ports')->nullOnDelete();

            $table->string('place_of_delivery')->nullable();
            $table->foreignId('courier_id')->nullable()
                ->constrained()->nullOnDelete();

            $table->decimal('subtotal', 18, 4)->default(0);
            $table->decimal('discount_amount', 18, 4)->default(0);
            $table->decimal('other_charges', 18, 4)->default(0);
            $table->decimal('total_amount', 18, 4)->default(0);
            $table->string('total_amount_in_words', 500)->nullable();

            $table->enum('status', ['draft', 'confirmed', 'submitted', 'paid', 'cancelled'])
                ->default('draft');

            $table->text('remarks')->nullable();
            $table->text('internal_notes')->nullable();

            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('commercial_invoices');
    }
};