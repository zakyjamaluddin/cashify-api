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
        Schema::create('wallet_members', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('wallet_id');
            $table->uuid('user_id');
            $table->enum('role', ['Viewer', 'Member'])->default('Member');
            $table->timestamp('joined_at')->useCurrent();

            $table->unique(['wallet_id', 'user_id'], 'unique_wallet_user');
            $table->foreign('wallet_id')->references('id')->on('wallets')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

            $table->index('user_id', 'idx_wallet_members_user');
            $table->index('wallet_id', 'idx_wallet_members_wallet');
            $table->index('role', 'idx_wallet_members_role');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wallet_members');
    }
};



