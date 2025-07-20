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
        Schema::create('mobile_money_payments', function (Blueprint $table) {
            $table->id();
            $table->string('transaction_id')->unique();
            $table->string('external_transaction_id')->nullable(); // from mobile money provider
            $table->foreignId('voucher_plan_id')->constrained()->onDelete('cascade');
            $table->foreignId('voucher_id')->nullable()->constrained()->onDelete('set null');
            $table->string('phone_number');
            $table->decimal('amount', 10, 2);
            $table->decimal('commission', 10, 2)->default(0);
            $table->enum('provider', ['mtn_mobile_money', 'airtel_money', 'safaricom_mpesa', 'vodacom_mpesa', 'tigo_pesa', 'orange_money']);
            $table->enum('status', ['pending', 'success', 'failed', 'cancelled', 'refunded'])->default('pending');
            $table->text('callback_response')->nullable();
            $table->string('reference_number')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->text('failure_reason')->nullable();
            $table->integer('retry_count')->default(0);
            $table->timestamps();
            
            $table->index(['phone_number', 'status']);
            $table->index(['provider', 'status']);
            $table->index(['transaction_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mobile_money_payments');
    }
};
