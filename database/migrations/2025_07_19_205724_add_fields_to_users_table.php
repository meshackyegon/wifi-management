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
        Schema::table('users', function (Blueprint $table) {
            $table->string('phone')->nullable()->after('email');
            $table->string('national_id')->nullable()->after('phone');
            $table->string('passport_number')->nullable()->after('national_id');
            $table->enum('user_type', ['admin', 'agent', 'customer'])->default('customer')->after('passport_number');
            $table->decimal('balance', 10, 2)->default(0)->after('user_type');
            $table->decimal('commission_rate', 5, 2)->default(3.00)->after('balance');
            $table->boolean('is_verified')->default(false)->after('commission_rate');
            $table->timestamp('phone_verified_at')->nullable()->after('is_verified');
            $table->string('verification_code')->nullable()->after('phone_verified_at');
            $table->boolean('is_active')->default(true)->after('verification_code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'phone', 'national_id', 'passport_number', 'user_type', 
                'balance', 'commission_rate', 'is_verified', 'phone_verified_at',
                'verification_code', 'is_active'
            ]);
        });
    }
};
