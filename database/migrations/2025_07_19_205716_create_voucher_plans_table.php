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
        Schema::create('voucher_plans', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->decimal('price', 10, 2);
            $table->integer('duration_hours')->nullable(); // null for unlimited time
            $table->bigInteger('data_limit_mb')->nullable(); // null for unlimited data
            $table->integer('bandwidth_limit_kbps')->nullable(); // null for no bandwidth limit
            $table->boolean('is_active')->default(true);
            $table->json('allowed_days')->nullable(); // days of week allowed
            $table->time('start_time')->nullable(); // start time for access
            $table->time('end_time')->nullable(); // end time for access
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('voucher_plans');
    }
};
