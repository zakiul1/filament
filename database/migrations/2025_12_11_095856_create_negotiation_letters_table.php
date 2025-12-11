<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('negotiation_letters', function (Blueprint $table) {
            $table->id();

            $table->string('letter_number')->unique();
            $table->date('letter_date');

            // Linked documents
            $table->foreignId('commercial_invoice_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('lc_receive_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('beneficiary_company_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('customer_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('currency_id')->nullable()->constrained()->nullOnDelete();

            // Values
            $table->decimal('invoice_amount', 15, 2)->nullable();
            $table->decimal('net_payable_amount', 15, 2)->nullable();
            $table->decimal('deductions', 15, 2)->default(0);

            // Bank info
            $table->string('bank_name')->nullable();
            $table->string('bank_branch')->nullable();
            $table->string('swift_code')->nullable();

            // Remarks
            $table->text('remarks')->nullable();

            $table->string('status')->default('draft');

            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamps();
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('negotiation_letters');
    }
};