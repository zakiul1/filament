<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('lc_receives', function (Blueprint $table) {
            $table->foreignId('proforma_invoice_id')
                ->nullable()
                ->after('reference_pi_number')
                ->constrained('proforma_invoices')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('lc_receives', function (Blueprint $table) {
            $table->dropConstrainedForeignId('proforma_invoice_id');
        });
    }
};