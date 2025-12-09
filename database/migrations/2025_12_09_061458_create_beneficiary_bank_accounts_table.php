<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('beneficiary_bank_accounts', function (Blueprint $table) {
            $table->id();

            $table->foreignId('beneficiary_company_id')
                ->constrained('beneficiary_companies')
                ->cascadeOnDelete();

            $table->foreignId('bank_branch_id')
                ->constrained('bank_branches')
                ->cascadeOnDelete();

            $table->foreignId('currency_id')
                ->nullable()
                ->constrained('currencies')
                ->nullOnDelete();

            // Bank account details
            $table->string('account_title');           // SIATEX (BD) LTD
            $table->string('account_number');          // 1234567890

            $table->string('iban', 34)->nullable();    // For EU etc.
            $table->string('swift_code', 11)->nullable(); // Override if different from branch
            $table->string('routing_number', 20)->nullable(); // ABA / routing

            // LC / garments-specific behaviour
            $table->boolean('is_lc_account')->default(true);     // For LC docs
            $table->boolean('is_tt_account')->default(true);     // For TT / non-LC
            $table->boolean('is_default')->default(false);       // Default for that beneficiary+currency

            $table->boolean('is_active')->default(true);
            $table->text('notes')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('beneficiary_bank_accounts');
    }
};