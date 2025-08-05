<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // For SQLite, we need to recreate the table with updated constraints
        // First, create a temporary table with the new structure
        Schema::create('mobile_money_payments_temp', function (Blueprint $table) {
            $table->id();
            $table->string('transaction_id')->unique();
            $table->string('external_transaction_id')->nullable();
            $table->foreignId('voucher_plan_id')->constrained()->onDelete('cascade');
            $table->foreignId('voucher_id')->nullable()->constrained()->onDelete('set null');
            $table->string('phone_number');
            $table->decimal('amount', 10, 2);
            $table->decimal('commission', 10, 2)->default(0);
            $table->enum('provider', ['mtn_mobile_money', 'airtel_money', 'safaricom_mpesa', 'vodacom_mpesa', 'tigo_pesa', 'orange_money', 'cash']);
            $table->string('payment_method')->default('mobile_money');
            $table->string('cash_received_by')->nullable();
            $table->decimal('cash_amount_received', 10, 2)->nullable();
            $table->decimal('change_given', 10, 2)->default(0);
            $table->text('payment_notes')->nullable();
            $table->timestamp('cash_received_at')->nullable();
            $table->enum('status', ['pending', 'success', 'failed', 'cancelled', 'refunded', 'pending_cash'])->default('pending');
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

        // Copy data from original table to temp table
        DB::statement('INSERT INTO mobile_money_payments_temp 
            (id, transaction_id, external_transaction_id, voucher_plan_id, voucher_id, 
             phone_number, amount, commission, provider, payment_method, cash_received_by, 
             cash_amount_received, change_given, payment_notes, cash_received_at, status, 
             callback_response, reference_number, paid_at, failure_reason, retry_count, 
             created_at, updated_at)
            SELECT 
             id, transaction_id, external_transaction_id, voucher_plan_id, voucher_id,
             phone_number, amount, commission, provider, payment_method, cash_received_by,
             cash_amount_received, change_given, payment_notes, cash_received_at, status,
             callback_response, reference_number, paid_at, failure_reason, retry_count,
             created_at, updated_at
            FROM mobile_money_payments');

        // Drop original table
        Schema::dropIfExists('mobile_money_payments');

        // Rename temp table to original name
        Schema::rename('mobile_money_payments_temp', 'mobile_money_payments');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Recreate table without cash support
        Schema::create('mobile_money_payments_temp', function (Blueprint $table) {
            $table->id();
            $table->string('transaction_id')->unique();
            $table->string('external_transaction_id')->nullable();
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

        // Copy non-cash data back
        DB::statement('INSERT INTO mobile_money_payments_temp 
            (id, transaction_id, external_transaction_id, voucher_plan_id, voucher_id, 
             phone_number, amount, commission, provider, status, callback_response, 
             reference_number, paid_at, failure_reason, retry_count, created_at, updated_at)
            SELECT 
             id, transaction_id, external_transaction_id, voucher_plan_id, voucher_id,
             phone_number, amount, commission, provider, status, callback_response,
             reference_number, paid_at, failure_reason, retry_count, created_at, updated_at
            FROM mobile_money_payments 
            WHERE provider != "cash"');

        Schema::dropIfExists('mobile_money_payments');
        Schema::rename('mobile_money_payments_temp', 'mobile_money_payments');
    }
};
