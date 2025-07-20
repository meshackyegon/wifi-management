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
        Schema::create('sms_logs', function (Blueprint $table) {
            $table->id();
            $table->string('phone_number');
            $table->text('message');
            $table->enum('provider', ['africastalking', 'twilio'])->default('africastalking');
            $table->enum('status', ['pending', 'sent', 'failed', 'delivered'])->default('pending');
            $table->string('external_id')->nullable(); // provider's message ID
            $table->foreignId('voucher_id')->nullable()->constrained()->onDelete('set null');
            $table->decimal('cost', 8, 4)->default(0); // SMS cost
            $table->text('error_message')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->integer('retry_count')->default(0);
            $table->timestamps();
            
            $table->index(['phone_number', 'status']);
            $table->index(['voucher_id']);
            $table->index(['status', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sms_logs');
    }
};
