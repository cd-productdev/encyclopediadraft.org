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
        Schema::create('date_formats', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique(); // e.g., "ISO 8601", "US Standard", "European"
            $table->string('format')->nullable(); // PHP date format string e.g., "Y-m-d", "d/m/Y", "F j, Y" (null for system default)
            $table->string('description')->nullable(); // Human readable description
            $table->string('example')->nullable(); // Example output e.g., "2024-01-15"
            $table->string('locale')->nullable()->default('en'); // Locale code e.g., "en", "ur", "ar"
            $table->boolean('is_default')->default(false); // Default format flag
            $table->boolean('is_active')->default(true); // Active status
            $table->integer('sort_order')->default(0); // Display order
            $table->string('type')->default('date'); // Type: 'date', 'datetime', 'time'
            $table->timestamps();

            // Indexes for better query performance
            $table->index('is_active');
            $table->index('is_default');
            $table->index('locale');
            $table->index('type');
            $table->index('sort_order');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('date_formats');
    }
};
