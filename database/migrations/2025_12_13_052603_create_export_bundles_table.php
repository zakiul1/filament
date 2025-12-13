<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('export_bundles', function (Blueprint $table) {
            $table->id();

            $table->foreignId('commercial_invoice_id')
                ->nullable()
                ->constrained('commercial_invoices')
                ->nullOnDelete();

            $table->string('bundle_no', 100)->unique();
            $table->date('bundle_date')->nullable();
            $table->string('status', 30)->default('generated'); // draft/generated/archived

            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('export_bundles');
    }
};