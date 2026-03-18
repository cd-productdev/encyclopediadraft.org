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
        Schema::create('redirects', function (Blueprint $table) {
            $table->id();
            $table->string('from_slug')->unique(); // Old slug
            $table->string('to_slug'); // New slug
            $table->foreignId('article_id')->constrained('articles')->onDelete('cascade');
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->text('reason')->nullable(); // Reason for redirect
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            // Indexes
            $table->index('from_slug');
            $table->index('to_slug');
            $table->index('article_id');
            $table->index('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('redirects');
    }
};
