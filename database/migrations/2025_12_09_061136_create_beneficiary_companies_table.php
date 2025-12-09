<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('beneficiary_companies', function (Blueprint $table) {
            $table->id();

            // Core identity
            $table->string('name');                          // Official name (e.g. SIATEX (BD) LTD)
            $table->string('short_name', 50)->nullable();    // SIATEX, APTEX, DOTTA
            $table->string('trade_name')->nullable();        // Trade/brand name if different
            $table->string('group_name')->nullable();        // Group / parent name (optional)

            // Address & location
            $table->string('address_line_1')->nullable();
            $table->string('address_line_2')->nullable();
            $table->string('city', 100)->nullable();
            $table->string('state', 100)->nullable();
            $table->string('postal_code', 20)->nullable();
            $table->foreignId('country_id')
                ->nullable()
                ->constrained('countries')
                ->nullOnDelete();

            // Contact info
            $table->string('phone', 50)->nullable();
            $table->string('mobile', 50)->nullable();
            $table->string('email')->nullable();
            $table->string('website')->nullable();

            // Garments / export compliance fields (future-proof)
            $table->string('erc_no')->nullable();           // Export Registration Certificate
            $table->string('irc_no')->nullable();           // Import Registration Certificate (if needed)
            $table->string('bin_no')->nullable();           // Business Identification Number
            $table->string('vat_reg_no')->nullable();       // VAT Registration
            $table->string('tin_no')->nullable();           // Tax Identification Number
            $table->string('bond_license_no')->nullable();  // Bonded warehouse license (if any)

            // Contact person - for LC / docs communication
            $table->string('contact_person_name')->nullable();
            $table->string('contact_person_designation')->nullable();
            $table->string('contact_person_phone', 50)->nullable();
            $table->string('contact_person_email')->nullable();

            // Defaults & flags
            $table->foreignId('default_currency_id')
                ->nullable()
                ->constrained('currencies')
                ->nullOnDelete();

            $table->boolean('is_default')->default(false); // main Siatex exporter
            $table->boolean('is_active')->default(true);

            $table->text('notes')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('beneficiary_companies');
    }
};