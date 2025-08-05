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
        Schema::table('mobile_money_payments', function (Blueprint $table) {
            // Add fields for cash payments
            $table->string('payment_method')->default('mobile_money')->after('provider');
            $table->string('cash_received_by')->nullable()->after('payment_method'); // Agent/Admin who received cash
            $table->decimal('cash_amount_received', 10, 2)->nullable()->after('cash_received_by');
            $table->decimal('change_given', 10, 2)->default(0)->after('cash_amount_received');
            $table->text('payment_notes')->nullable()->after('change_given');
            $table->timestamp('cash_received_at')->nullable()->after('payment_notes');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mobile_money_payments', function (Blueprint $table) {
            $table->dropColumn([
                'payment_method',
                'cash_received_by',
                'cash_amount_received',
                'change_given',
                'payment_notes',
                'cash_received_at'
            ]);
        });
    }
};
