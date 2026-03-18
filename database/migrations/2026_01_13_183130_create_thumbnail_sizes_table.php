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
        Schema::create('thumbnail_sizes', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique(); // Display name e.g., "120px", "300px"
            $table->integer('size'); // Size in pixels (for square thumbnails)
            $table->string('description')->nullable(); // Description of the size
            $table->boolean('is_default')->default(false); // Default size flag
            $table->boolean('is_active')->default(true); // Active status
            $table->integer('sort_order')->default(0); // Display order
            $table->timestamps();

            // Indexes for better query performance
            $table->index('is_active');
            $table->index('is_default');
            $table->index('sort_order');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('thumbnail_sizes');
    }
};
