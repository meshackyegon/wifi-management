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
            $table->id();
            $table->string('transaction_id')->unique();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->enum('type', ['voucher_sale', 'commission', 'withdrawal', 'refund', 'deposit']);
            $table->decimal('amount', 10, 2);
            $table->decimal('commission', 10, 2)->default(0);
            $table->decimal('balance_before', 10, 2);
            $table->decimal('balance_after', 10, 2);
            $table->string('description')->nullable();
            $table->morphs('transactionable'); // polymorphic relation (voucher, payment, etc.)
            $table->enum('status', ['pending', 'completed', 'failed', 'cancelled'])->default('completed');
            $table->json('metadata')->nullable(); // additional transaction data
            $table->timestamps();
            
            $table->index(['user_id', 'type']);
            $table->index(['status', 'created_at']);
            $table->index('transaction_id');
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
