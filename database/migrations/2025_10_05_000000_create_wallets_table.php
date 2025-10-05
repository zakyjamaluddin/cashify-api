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
        Schema::create('wallets', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name', 100);
            $table->decimal('current_balance', 15, 2)->default(0);
            $table->enum('privacy', ['Private', 'Shared'])->default('Private');
            $table->uuid('admin_id');
            $table->integer('member_count')->default(1);
            $table->timestamps();

            $table->foreign('admin_id')->references('id')->on('users')->onDelete('cascade');

            $table->index('admin_id', 'idx_wallets_admin');
            $table->index('privacy', 'idx_wallets_privacy');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wallets');
    }
};



