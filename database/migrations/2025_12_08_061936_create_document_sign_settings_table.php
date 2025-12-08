<?php


use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;


return new class extends Migration {
    public function up(): void
    {
        Schema::create('document_sign_settings', function (Blueprint $table) {
            $table->id();
            $table->string('document_type'); // PI, COMMERCIAL_INVOICE, LC_TRANSFER, etc.
            $table->foreignId('signatory_id')->constrained()->cascadeOnDelete();
            $table->boolean('show_signature')->default(true);
            $table->boolean('show_seal')->default(false);
            $table->timestamps();

            $table->unique(['document_type', 'signatory_id']);
        });
    }



    public function down(): void
    {
        Schema::dropIfExists('document_sign_settings');
    }
};