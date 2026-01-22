<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('bank_accounts', function (Blueprint $table) {
            $table->id();

            $table->foreignId('bank_branch_id')
                ->constrained('bank_branches')
                ->cascadeOnDelete();

            // ✅ Country is REQUIRED (you already have countries module)
            $table->foreignId('country_id')
                ->constrained('countries')
                ->restrictOnDelete();

            // ✅ Currency optional
            $table->foreignId('currency_id')
                ->nullable()
                ->constrained('currencies')
                ->nullOnDelete();

            // Account details
            $table->string('account_title');              // e.g. SIATEX (BD) LTD
            $table->string('account_number');             // e.g. 1234567890

            $table->string('iban', 34)->nullable();
            $table->string('swift_code', 11)->nullable(); // override if needed
            $table->string('routing_number', 20)->nullable();

            $table->boolean('is_active')->default(true);
            $table->text('notes')->nullable();

            // Optional audit (no FK to keep migration order safe)
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();

            $table->timestamps();

            $table->index(['bank_branch_id']);
            $table->index(['country_id']);
            $table->index(['currency_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bank_accounts');
    }
};