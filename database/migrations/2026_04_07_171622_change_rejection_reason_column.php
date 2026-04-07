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
        DB::table('articles')
            ->whereNotNull('rejection_reason')
            ->whereRaw("rejection_reason NOT LIKE '[\"%'") // Only if not already JSON
            ->update([
                'rejection_reason' => DB::raw('JSON_ARRAY(rejection_reason)'),
            ]);

        // 2. Now safely change the column to JSON
        Schema::table('articles', function (Blueprint $table) {
            $table->json('rejection_reason')->nullable()->change();
        });
        //
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('articles', function (Blueprint $table) {
            $table->string('rejection_reason', 1000)->nullable()->change();
        });
    }
};
