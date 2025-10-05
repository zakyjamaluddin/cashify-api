<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('recurring_schedules', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('wallet_id');
            $table->enum('type', ['Income', 'Expense']);
            $table->uuid('category_id');
            $table->decimal('amount', 15, 2);
            $table->text('description')->nullable();
            $table->enum('interval_type', ['Daily', 'Weekly', 'Monthly', 'Yearly']);
            $table->timestamp('start_date');
            $table->timestamp('end_date')->nullable();
            $table->timestamp('next_run_date');
            $table->integer('reminder_before_days')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->foreign('wallet_id')->references('id')->on('wallets')->onDelete('cascade');
            $table->foreign('category_id')->references('id')->on('categories');

            $table->index(['next_run_date', 'is_active'], 'idx_recurring_next_run');
            $table->index('wallet_id', 'idx_recurring_wallet');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('recurring_schedules');
    }
};



