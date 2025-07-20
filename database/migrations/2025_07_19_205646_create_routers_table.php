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
        Schema::create('routers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('ip_address');
            $table->string('api_username');
            $table->string('api_password');
            $table->integer('api_port')->default(8728);
            $table->enum('type', ['mikrotik', 'coovachilli', 'freeradius'])->default('mikrotik');
            $table->string('location')->nullable();
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamp('last_connected_at')->nullable();
            $table->json('settings')->nullable(); // additional router-specific settings
            $table->string('redirect_url')->nullable(); // for captive portal
            $table->boolean('block_social_media')->default(false);
            $table->boolean('block_streaming')->default(false);
            $table->boolean('prevent_hotspot_sharing')->default(true);
            $table->integer('max_concurrent_users')->nullable();
            $table->timestamps();
            
            $table->unique('ip_address');
            $table->index(['is_active', 'type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('routers');
    }
};
