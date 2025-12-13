<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        // ✅ STEP 1: Make column flexible first (works even if current is ENUM)
        DB::statement("
            ALTER TABLE bill_of_exchanges
            MODIFY boe_type VARCHAR(20) NULL
        ");

        // ✅ STEP 2: Normalize existing values into 'one' / 'two'
        DB::statement("
            UPDATE bill_of_exchanges
            SET boe_type = CASE
                WHEN boe_type IN ('one','ONE','One','1','boe1','boe_one','first','FIRST','First') THEN 'one'
                WHEN boe_type IN ('two','TWO','Two','2','boe2','boe_two','second','SECOND','Second') THEN 'two'
                ELSE 'one'
            END
        ");

        // ✅ STEP 3: Lock back to ENUM('one','two')
        DB::statement("
            ALTER TABLE bill_of_exchanges
            MODIFY boe_type ENUM('one','two') NOT NULL DEFAULT 'one'
        ");
    }

    public function down(): void
    {
        DB::statement("
            ALTER TABLE bill_of_exchanges
            MODIFY boe_type VARCHAR(20) NULL
        ");
    }
};