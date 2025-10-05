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
        Schema::create('users', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('email')->unique();
            $table->string('display_name', 100)->nullable();
            $table->string('password_hash', 255)->nullable();
            $table->boolean('is_email_verified')->default(false);
            $table->enum('subscription_status', ['Free', 'Premium'])->default('Free');
            $table->uuid('active_wallet_id')->nullable();
            $table->timestamps();

            $table->index('subscription_status', 'idx_users_subscription');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
