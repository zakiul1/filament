<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('incoterms', function (Blueprint $table) {
            $table->id();
            $table->string('code', 10);          // FOB, CIF, CFR...
            $table->string('name')->nullable();  // Optional long name
            $table->string('version', 10)->nullable(); // Incoterms 2020
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('incoterms');
    }
};