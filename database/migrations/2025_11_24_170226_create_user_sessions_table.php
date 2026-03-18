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
        Schema::create('user_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('ip_address', 45);
            $table->string('device_name')->nullable();
            $table->text('user_agent')->nullable();
            $table->string('city')->nullable();
            $table->string('country')->nullable();
            $table->string('token')->nullable(); // JWT token identifier
            $table->timestamp('login_at');
            $table->timestamp('last_login_at')->nullable();
            $table->timestamp('last_active_at')->nullable();
            $table->timestamp('logout_at')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['user_id', 'is_active']);
            $table->index('token');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_sessions');
    }
};
