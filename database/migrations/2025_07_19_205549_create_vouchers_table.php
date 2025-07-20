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
        Schema::create('vouchers', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('password')->nullable();
            $table->foreignId('voucher_plan_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null'); // who generated it
            $table->foreignId('router_id')->nullable()->constrained()->onDelete('set null'); // which router
            $table->enum('status', ['active', 'used', 'expired', 'disabled'])->default('active');
            $table->decimal('price', 10, 2);
            $table->decimal('commission', 10, 2)->default(0);
            $table->timestamp('used_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->string('used_by_phone')->nullable(); // phone number of user who used it
            $table->string('mac_address')->nullable(); // device MAC that used this voucher
            $table->integer('session_time_used')->default(0); // in seconds
            $table->bigInteger('data_used_mb')->default(0);
            $table->boolean('is_printed')->default(false);
            $table->timestamp('printed_at')->nullable();
            $table->timestamps();
            
            $table->index(['status', 'expires_at']);
            $table->index(['router_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vouchers');
    }
};
