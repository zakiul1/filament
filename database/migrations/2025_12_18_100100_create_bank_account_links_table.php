<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('bank_account_links', function (Blueprint $table) {
            $table->id();

            $table->foreignId('bank_account_id')
                ->constrained('bank_accounts')
                ->cascadeOnDelete();

            // Polymorphic owner: BeneficiaryCompany / Customer / Factory etc.
            $table->string('owner_type');
            $table->unsignedBigInteger('owner_id');

            $table->string('label')->nullable();          // Primary / USD / LC etc.

            // Same behavior flags you used in beneficiary_bank_accounts
            $table->boolean('is_lc_account')->default(true);
            $table->boolean('is_tt_account')->default(true);
            $table->boolean('is_default')->default(false);

            $table->boolean('is_active')->default(true);

            // Optional audit (no FK to keep migration order safe)
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();

            $table->timestamps();

            $table->index(['owner_type', 'owner_id']);
            $table->index(['bank_account_id']);

            // Prevent duplicate same account attached twice to same owner
            $table->unique(['bank_account_id', 'owner_type', 'owner_id'], 'uniq_bank_account_owner');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bank_account_links');
    }
};