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
        Schema::create('packing_lists', function (Blueprint $table) {
            $table->id();

            $table->string('pl_number')->unique();
            $table->date('pl_date');

            // Link to CI
            $table->foreignId('commercial_invoice_id')->nullable()->constrained()->nullOnDelete();

            // Optional LC link
            $table->foreignId('lc_receive_id')->nullable()->constrained()->nullOnDelete();

            // Buyer & Beneficiary (for quick access)
            $table->foreignId('customer_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('beneficiary_company_id')->nullable()->constrained()->nullOnDelete();

            // Summary fields
            $table->integer('total_cartons')->default(0);
            $table->integer('total_quantity')->default(0);

            $table->decimal('total_nw', 12, 2)->default(0);
            $table->decimal('total_gw', 12, 2)->default(0);
            $table->decimal('total_cbm', 12, 3)->default(0);

            $table->text('remarks')->nullable();
            $table->text('internal_notes')->nullable();

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
        Schema::dropIfExists('packing_lists');
    }
};