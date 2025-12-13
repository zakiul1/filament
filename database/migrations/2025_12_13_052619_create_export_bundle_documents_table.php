<?php

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

            // Document type key for UI grouping (CI, PL, NL, BOE_ONE, BOE_TWO)
            $table->string('doc_key', 30);

            // Polymorphic link to any document model
            $table->morphs('documentable'); // documentable_type + documentable_id

            $table->string('status', 30)->default('draft'); // draft/generated/printed/archived
            $table->timestamp('generated_at')->nullable();
            $table->timestamp('printed_at')->nullable();
            $table->unsignedInteger('print_count')->default(0);

            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();

            $table->timestamps();

            // Prevent duplicates in same bundle (example: one PL per bundle)
            $table->unique(['export_bundle_id', 'doc_key']);

            $table->index(['export_bundle_id', 'doc_key']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('export_bundle_documents');
    }
};