<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('shipment_modes', function (Blueprint $table) {
            $table->id();
            $table->string('name');               // Sea, Air, Courier
            $table->string('code', 10)->nullable(); // SEA, AIR, COURIER
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shipment_modes');
    }
};