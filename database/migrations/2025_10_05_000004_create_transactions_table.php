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
        Schema::create('transactions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('wallet_id');
            $table->enum('type', ['Income', 'Expense']);
            $table->uuid('category_id');
            $table->decimal('amount', 15, 2);
            $table->text('description')->nullable();
            $table->timestamp('date');
            $table->string('proof_url', 500)->nullable();
            $table->uuid('recorded_by');
            $table->boolean('is_recurring')->default(false);
            $table->uuid('recurring_schedule_id')->nullable();
            $table->timestamps();

            $table->foreign('wallet_id')->references('id')->on('wallets')->onDelete('cascade');
            $table->foreign('category_id')->references('id')->on('categories');
            $table->foreign('recorded_by')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('recurring_schedule_id')->references('id')->on('recurring_schedules')->onDelete('set null');

            $table->index(['wallet_id', 'date'], 'idx_transactions_wallet_date');
            $table->index('type', 'idx_transactions_type');
            $table->index('category_id', 'idx_transactions_category');
            $table->index('is_recurring', 'idx_transactions_recurring');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};



