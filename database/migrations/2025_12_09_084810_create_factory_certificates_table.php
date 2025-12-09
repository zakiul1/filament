<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('factory_certificates', function (Blueprint $table) {
            $table->id();
            $table->string('code', 50)->nullable();      // e.g. BSCI, WRAP, GOTS
            $table->string('name');                      // full name
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('factory_certificates');
    }
};