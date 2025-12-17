<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('export_bundle_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('export_bundle_id')->constrained()->cascadeOnDelete();

            $table->string('event'); // locked|printed|printed_all|submitted|couriered|bank_accepted|bank_rejected|unsubmitted
            $table->timestamp('event_at')->nullable();

            $table->string('ref')->nullable();  // submission_ref / courier_ref / bank_ref
            $table->text('notes')->nullable();

            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamps();

            $table->index(['export_bundle_id', 'event']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('export_bundle_events');
    }
};