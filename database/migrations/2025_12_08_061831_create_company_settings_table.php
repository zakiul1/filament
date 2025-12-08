<?php


use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;


return new class extends Migration {
    public function up(): void
    {
        Schema::create('company_settings', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('address')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->string('logo_path')->nullable();
            $table->string('seal_path')->nullable();
            $table->string('base_currency_code', 3)->default('USD'); // or BDT
            $table->timestamps();
        });
    }


    public function down(): void
    {
        Schema::dropIfExists('company_settings');
    }
};