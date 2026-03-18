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
        Schema::create('timezones', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique(); // Timezone identifier e.g., "America/New_York", "Asia/Karachi"
            $table->string('display_name'); // Human readable name e.g., "Eastern Time (US & Canada)"
            $table->string('abbreviation')->nullable(); // Timezone abbreviation e.g., "EST", "PST", "PKT"
            $table->string('offset'); // UTC offset e.g., "-05:00", "+05:00"
            $table->decimal('offset_hours', 4, 2); // Offset in hours e.g., -5.00, 5.00
            $table->string('region')->nullable(); // Region e.g., "America", "Asia", "Europe"
            $table->string('country')->nullable(); // Country code e.g., "US", "PK", "GB"
            $table->boolean('is_active')->default(true); // Active status
            $table->integer('sort_order')->default(0); // Display order
            $table->boolean('is_default')->default(false); // Default timezone flag
            $table->timestamps();

            // Indexes for better query performance
            $table->index('is_active');
            $table->index('is_default');
            $table->index('region');
            $table->index('country');
            $table->index('sort_order');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('timezones');
    }
};
