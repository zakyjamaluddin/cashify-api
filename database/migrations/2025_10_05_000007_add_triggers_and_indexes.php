<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Transactions trigger: only on MySQL; SQLite requires different syntax and often not needed in dev
        $driver = DB::getDriverName();
        if ($driver === 'mysql') {
            DB::unprepared(<<<SQL
            CREATE TRIGGER after_transaction_insert 
            AFTER INSERT ON transactions
            FOR EACH ROW
            BEGIN
                IF NEW.type = 'Income' THEN
                    UPDATE wallets 
                    SET current_balance = current_balance + NEW.amount 
                    WHERE id = NEW.wallet_id;
                ELSE
                    UPDATE wallets 
                    SET current_balance = current_balance - NEW.amount 
                    WHERE id = NEW.wallet_id;
                END IF;
            END
            SQL);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $driver = DB::getDriverName();
        if ($driver === 'mysql') {
            DB::unprepared('DROP TRIGGER IF EXISTS after_transaction_insert');
        }
    }
};


