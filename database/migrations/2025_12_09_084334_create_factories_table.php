<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('factories', function (Blueprint $table) {
            $table->id();

            // Identity
            $table->string('code', 50)->nullable();          // Internal code, optional
            $table->string('name');                          // Full legal factory name
            $table->string('short_name', 50)->nullable();    // Short label (for dropdowns)
            $table->string('factory_type', 50)->nullable();  // Knit / Woven / Sweater / Denim etc.

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

            // Main contact person at factory
            $table->string('contact_person_name')->nullable();
            $table->string('contact_person_designation')->nullable();
            $table->string('contact_person_phone', 50)->nullable();
            $table->string('contact_person_email')->nullable();

            // Capability / production info
            $table->unsignedSmallInteger('total_lines')->nullable();        // no. of lines
            $table->unsignedInteger('capacity_pcs_per_month')->nullable();  // approx capacity
            $table->text('capability_notes')->nullable();                   // products / gauges / fabric types

            // Factory images (multiple) - store paths as JSON
            $table->json('images')->nullable();                             // for multi image upload

            // For LC Transfer / payments (base currency)
            $table->foreignId('default_currency_id')
                ->nullable()
                ->constrained('currencies')
                ->nullOnDelete();

            // Status
            $table->boolean('is_active')->default(true);

            $table->text('notes')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('factories');
    }
};