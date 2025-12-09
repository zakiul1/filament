<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->id();

            // Identity
            $table->string('code', 50)->nullable();    // Optional internal code
            $table->string('name');                   // Legal name
            $table->string('short_name', 50)->nullable(); // Short label / nickname
            $table->string('buyer_group')->nullable();    // e.g. H&M Group, Inditex

            // Address
            $table->string('address_line_1')->nullable();
            $table->string('address_line_2')->nullable();
            $table->string('city', 100)->nullable();
            $table->string('state', 100)->nullable();
            $table->string('postal_code', 20)->nullable();

            $table->foreignId('country_id')
                ->nullable()
                ->constrained('countries')
                ->nullOnDelete();

            // Contact
            $table->string('phone', 50)->nullable();
            $table->string('email')->nullable();
            $table->string('website')->nullable();

            // Contact person (for LC / approvals / booking)
            $table->string('contact_person_name')->nullable();
            $table->string('contact_person_designation')->nullable();
            $table->string('contact_person_phone', 50)->nullable();
            $table->string('contact_person_email')->nullable();

            // Compliance / invoice details (used on CI / LC sometimes)
            $table->string('vat_reg_no')->nullable();    // VAT / TAX ID
            $table->string('eori_no')->nullable();       // EU EORI if relevant
            $table->string('registration_no')->nullable(); // Company registration no.

            // Default commercial terms
            $table->foreignId('default_currency_id')
                ->nullable()
                ->constrained('currencies')
                ->nullOnDelete();

            $table->foreignId('default_incoterm_id')
                ->nullable()
                ->constrained('incoterms')
                ->nullOnDelete();

            $table->foreignId('default_payment_term_id')
                ->nullable()
                ->constrained('payment_terms')
                ->nullOnDelete();

            $table->foreignId('default_shipment_mode_id')
                ->nullable()
                ->constrained('shipment_modes')
                ->nullOnDelete();

            // Usually destination port (their side)
            $table->foreignId('default_destination_port_id')
                ->nullable()
                ->constrained('ports')
                ->nullOnDelete();

            // Flags
            $table->boolean('is_active')->default(true);

            $table->text('notes')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};