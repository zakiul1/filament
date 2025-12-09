<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('bank_branches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bank_id')
                ->constrained('banks')
                ->cascadeOnDelete();

            $table->string('name');                   // Gulshan Branch
            $table->string('branch_code', 20)->nullable();
            $table->string('swift_code', 11)->nullable();

            $table->string('city', 100)->nullable();
            $table->foreignId('country_id')
                ->nullable()
                ->constrained('countries')
                ->nullOnDelete();

            $table->string('address')->nullable();

            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bank_branches');
    }
};