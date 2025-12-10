<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('lc_receives', function (Blueprint $table) {
            $table->id();

            // Basic LC info
            $table->string('lc_number', 100)->unique();
            $table->date('lc_date')->nullable();
            $table->string('lc_type', 50)->nullable(); // sight, usance, deferred, transferable etc.

            // Parties & banks
            $table->foreignId('customer_id')->constrained()->cascadeOnUpdate();
            $table->foreignId('customer_bank_id')->nullable()->constrained('customer_banks')->nullOnDelete();
            $table->foreignId('beneficiary_company_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('beneficiary_bank_account_id')->nullable()->constrained()->nullOnDelete();

            // Amount & currency
            $table->foreignId('currency_id')->constrained()->cascadeOnUpdate();
            $table->decimal('lc_amount', 18, 4)->default(0);
            $table->decimal('tolerance_plus', 5, 2)->default(0);  // +10%
            $table->decimal('tolerance_minus', 5, 2)->default(0); // -10%
            $table->string('lc_amount_in_words', 500)->nullable();

            // Validity & shipment constraints
            $table->date('expiry_date')->nullable();
            $table->date('last_shipment_date')->nullable();
            $table->unsignedTinyInteger('presentation_days')->nullable(); // days after shipment

            $table->foreignId('incoterm_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('shipment_mode_id')->nullable()->constrained()->nullOnDelete();

            $table->foreignId('port_of_loading_id')->nullable()->constrained('ports')->nullOnDelete();
            $table->foreignId('port_of_discharge_id')->nullable()->constrained('ports')->nullOnDelete();

            $table->boolean('partial_shipment_allowed')->default(true);
            $table->boolean('transshipment_allowed')->default(true);

            // Other commercial references
            $table->string('reference_pi_number', 100)->nullable();
            $table->string('reimbursement_bank', 255)->nullable();

            // Status
            $table->string('status', 30)->default('draft');
            // draft, received, confirmed, cancelled, closed, expired

            // Notes
            $table->text('remarks')->nullable();
            $table->text('internal_notes')->nullable();

            // Audit
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lc_receives');
    }
};