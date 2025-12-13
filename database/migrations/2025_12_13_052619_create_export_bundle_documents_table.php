<?php

// database/migrations/xxxx_xx_xx_create_export_bundle_documents_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('export_bundle_documents', function (Blueprint $table) {
            $table->id();

            $table->foreignId('export_bundle_id')
                ->constrained('export_bundles')
                ->cascadeOnDelete();

            $table->string('document_type', 50); // commercial_invoice, packing_list, boe_1, boe_2, negotiation
            $table->unsignedBigInteger('document_id')->nullable(); // if you want to store linked record IDs
            $table->string('print_route', 150)->nullable();         // e.g. admin.trade.commercial-invoices.print
            $table->string('status', 30)->default('ready');         // ready/missing

            $table->timestamps();

            $table->unique(['export_bundle_id', 'document_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('export_bundle_documents');
    }
};