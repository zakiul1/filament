<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('export_bundle_documents', function (Blueprint $table) {
            if (Schema::hasColumn('export_bundle_documents', 'document_type')) {
                $table->dropColumn('document_type');
            }
        });
    }

    public function down(): void
    {
        Schema::table('export_bundle_documents', function (Blueprint $table) {
            $table->string('document_type', 50)->nullable();
        });
    }
};