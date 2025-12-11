<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('packing_list_items', function (Blueprint $table) {
            $table->id();

            $table->foreignId('packing_list_id')->constrained()->cascadeOnDelete();

            $table->integer('line_no')->default(1);
            $table->string('description')->nullable();

            $table->integer('carton_from');
            $table->integer('carton_to');

            $table->integer('total_cartons')->default(0);

            $table->integer('qty_per_carton')->default(0);
            $table->integer('total_qty')->default(0);

            $table->decimal('net_weight', 12, 2)->default(0);
            $table->decimal('gross_weight', 12, 2)->default(0);
            $table->decimal('cbm', 12, 3)->default(0);

            $table->timestamps();
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('packing_list_items');
    }
};