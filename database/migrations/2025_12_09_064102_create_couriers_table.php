<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('couriers', function (Blueprint $table) {
            $table->id();

            $table->string('name');                 // DHL Express, FedEx, UPS, Aramex, SA Paribahan etc.
            $table->string('short_name', 50)->nullable(); // DHL, FEDEX, UPS

            // For grouping / reporting
            $table->enum('service_type', ['international', 'local', 'both'])
                ->default('international');

            // Contact & account info
            $table->string('account_number', 100)->nullable();
            $table->string('contact_person')->nullable();
            $table->string('contact_phone', 50)->nullable();
            $table->string('contact_email')->nullable();

            // Address / region (optional but future-proof)
            $table->string('address_line_1')->nullable();
            $table->string('address_line_2')->nullable();
            $table->string('city', 100)->nullable();
            $table->string('state', 100)->nullable();
            $table->string('postal_code', 20)->nullable();

            $table->foreignId('country_id')
                ->nullable()
                ->constrained('countries')
                ->nullOnDelete();

            // Online tracking support
            $table->string('website')->nullable();
            $table->string('tracking_url_template')->nullable();
            // e.g. https://www.dhl.com/global-en/home/tracking.html?tracking-id={TRACKING_NO}

            // Capabilities (for future)
            $table->boolean('supports_documents')->default(true);   // LC docs
            $table->boolean('supports_parcels')->default(true);     // samples
            $table->boolean('supports_import')->default(true);      // inbound courier
            $table->boolean('supports_export')->default(true);      // outbound courier

            // Behavior flags
            $table->boolean('is_default')->default(false);
            $table->boolean('is_active')->default(true);

            $table->text('notes')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('couriers');
    }
};