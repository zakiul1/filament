<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('factory_certificate_assignments', function (Blueprint $table) {
            $table->id();

            $table->foreignId('factory_id')
                ->constrained('factories')
                ->cascadeOnDelete();

            $table->foreignId('factory_certificate_id')
                ->constrained('factory_certificates')
                ->cascadeOnDelete();

            $table->string('file_path')->nullable();   // PDF/Image of certificate
            $table->date('issued_at')->nullable();     // Issue date
            $table->date('expires_at')->nullable();    // Expiry date
            $table->boolean('is_active')->default(true);
            $table->text('notes')->nullable();

            $table->timestamps();

            // Avoid duplicate certificate assignment
            $table->unique(['factory_id', 'factory_certificate_id'], 'factory_certificate_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('factory_certificate_assignments');
    }
};