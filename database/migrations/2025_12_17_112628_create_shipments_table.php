<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('shipments', function (Blueprint $table) {
            $table->id();

            $table->foreignId('commercial_invoice_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('export_bundle_id')->nullable()->constrained()->nullOnDelete();

            $table->string('shipment_no')->unique(); // SHP-2025-00001
            $table->date('shipment_date')->nullable();

            $table->string('mode')->default('sea'); // sea|air|courier
            $table->string('bl_awb_no')->nullable();

            $table->string('vessel_name')->nullable();
            $table->string('voyage_no')->nullable();

            $table->string('container_no')->nullable();
            $table->string('seal_no')->nullable();

            $table->string('port_of_loading')->nullable();
            $table->string('port_of_discharge')->nullable();
            $table->string('final_destination')->nullable();

            $table->date('etd')->nullable();
            $table->date('eta')->nullable();

            $table->string('forwarder_name')->nullable();
            $table->string('forwarder_contact')->nullable();

            $table->text('remarks')->nullable();
            $table->string('status')->default('draft'); // draft|booked|shipped|delivered|cancelled

            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamps();

            $table->index(['export_bundle_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shipments');
    }
};