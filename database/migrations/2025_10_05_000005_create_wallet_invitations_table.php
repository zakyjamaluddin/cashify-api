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
        Schema::create('wallet_invitations', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('wallet_id');
            $table->string('recipient_email', 255);
            $table->enum('assigned_role', ['Editor', 'Viewer']);
            $table->string('invite_token', 100)->unique();
            $table->uuid('invited_by');
            $table->enum('status', ['Pending', 'Accepted', 'Expired'])->default('Pending');
            $table->timestamp('expires_at');
            $table->timestamp('created_at')->useCurrent();

            $table->foreign('wallet_id')->references('id')->on('wallets')->onDelete('cascade');
            $table->foreign('invited_by')->references('id')->on('users')->onDelete('cascade');

            $table->index('invite_token', 'idx_invitations_token');
            $table->index('recipient_email', 'idx_invitations_email');
            $table->index('status', 'idx_invitations_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wallet_invitations');
    }
};



