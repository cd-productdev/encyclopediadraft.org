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
        Schema::table('articles', function (Blueprint $table) {
            $table->enum('status', ['draft', 'pending', 'published', 'rejected'])->default('draft')->after('content');
            $table->timestamp('submitted_at')->nullable()->after('status');
            $table->timestamp('published_at')->nullable()->after('submitted_at');
            $table->unsignedBigInteger('reviewed_by')->nullable()->after('published_at');
            $table->text('rejection_reason')->nullable()->after('reviewed_by');
            
            $table->foreign('reviewed_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('articles', function (Blueprint $table) {
            $table->dropForeign(['reviewed_by']);
            $table->dropColumn(['status', 'submitted_at', 'published_at', 'reviewed_by', 'rejection_reason']);
        });
    }
};
