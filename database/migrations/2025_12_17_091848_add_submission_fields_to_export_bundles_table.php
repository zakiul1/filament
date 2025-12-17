<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('export_bundles', function (Blueprint $table) {
            $table->timestamp('submitted_at')->nullable()->after('locked_by');
            $table->unsignedBigInteger('submitted_by')->nullable()->after('submitted_at');

            $table->string('submission_ref')->nullable()->after('submitted_by'); // optional
            $table->string('bank_ack_file_path')->nullable()->after('submission_ref'); // optional uploaded PDF

            $table->foreign('submitted_by')->references('id')->on('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('export_bundles', function (Blueprint $table) {
            $table->dropForeign(['submitted_by']);
            $table->dropColumn([
                'submitted_at',
                'submitted_by',
                'submission_ref',
                'bank_ack_file_path',
            ]);
        });
    }
};