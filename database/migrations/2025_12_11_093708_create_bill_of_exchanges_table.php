<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('bill_of_exchanges', function (Blueprint $table) {
            $table->id();

            $table->string('boe_number', 100)->unique();
            $table->enum('boe_type', ['FIRST', 'SECOND'])->default('FIRST');
            $table->date('issue_date');
            $table->unsignedInteger('tenor_days')->default(0);
            $table->date('maturity_date')->nullable();

            $table->foreignId('customer_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('beneficiary_company_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('lc_receive_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('commercial_invoice_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('currency_id')->nullable()->constrained()->nullOnDelete();

            $table->decimal('amount', 18, 2)->default(0);
            $table->string('amount_in_words', 500)->nullable();

            $table->string('drawee_name', 255)->nullable();
            $table->string('drawee_bank_name', 255)->nullable();
            $table->string('place_of_drawing', 255)->nullable();
            $table->text('drawee_address')->nullable();
            $table->text('drawee_bank_address')->nullable();

            $table->string('status', 50)->default('draft');

            $table->text('remarks')->nullable();
            $table->text('internal_notes')->nullable();

            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bill_of_exchanges');
    }
};