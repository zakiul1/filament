<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('export_bundle_documents', function (Blueprint $table) {

            // ✅ if old column exists, rename it
            if (
                Schema::hasColumn('export_bundle_documents', 'document_type')
                && !Schema::hasColumn('export_bundle_documents', 'doc_key')
            ) {
                $table->renameColumn('document_type', 'doc_key');
            }

            // ✅ if old column exists, rename it
            if (
                Schema::hasColumn('export_bundle_documents', 'document_id')
                && !Schema::hasColumn('export_bundle_documents', 'documentable_id')
            ) {
                $table->renameColumn('document_id', 'documentable_id');
            }

            // ✅ ensure morph type exists
            if (!Schema::hasColumn('export_bundle_documents', 'documentable_type')) {
                $table->string('documentable_type')->nullable()->after('documentable_id');
            }

            // ✅ optional tracking columns
            if (!Schema::hasColumn('export_bundle_documents', 'generated_at')) {
                $table->dateTime('generated_at')->nullable();
            }
            if (!Schema::hasColumn('export_bundle_documents', 'printed_at')) {
                $table->dateTime('printed_at')->nullable();
            }
            if (!Schema::hasColumn('export_bundle_documents', 'print_count')) {
                $table->unsignedInteger('print_count')->default(0);
            }

            // ✅ status default
            if (Schema::hasColumn('export_bundle_documents', 'status')) {
                // keep it
            } else {
                $table->string('status', 30)->default('missing');
            }
        });

        // ✅ Make sure existing rows have doc_key
        DB::table('export_bundle_documents')
            ->whereNull('doc_key')
            ->update(['doc_key' => 'commercial_invoice']);
    }

    public function down(): void
    {
        // you can leave empty or reverse if you want
    }
};