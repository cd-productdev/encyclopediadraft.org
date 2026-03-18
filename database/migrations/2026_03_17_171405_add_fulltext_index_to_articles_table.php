<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add FULLTEXT indexes for semantic search
        DB::statement('ALTER TABLE articles ADD FULLTEXT INDEX articles_search_index (title, content, summary)');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop the FULLTEXT index
        DB::statement('ALTER TABLE articles DROP INDEX articles_search_index');
    }
};
