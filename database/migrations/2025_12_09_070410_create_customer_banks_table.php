<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('customer_banks', function (Blueprint $table) {
            $table->id();

            $table->foreignId('customer_id')
                ->constrained('customers')
                ->cascadeOnDelete();

            $table->foreignId('bank_branch_id')
                ->constrained('bank_branches')
                ->cascadeOnDelete();

            // Label to distinguish if same bank used multiple ways
            $table->string('label')->nullable(); // e.g. Main LC Bank, Backup Bank

            // Behaviour flags
            $table->boolean('is_default_lc')->default(true);  // used when choosing LC issuing bank
            $table->boolean('is_default_tt')->default(false); // if used for TT settlements

            $table->boolean('is_active')->default(true);

            $table->text('notes')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('customer_banks');
    }
};