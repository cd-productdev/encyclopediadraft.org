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
        Schema::create('uploads', function (Blueprint $table) {
            $table->id();
            $table->foreignId('uploader_id')->constrained('users')->onDelete('cascade');
            $table->string('original_filename');
            $table->string('destination_filename')->nullable();
            $table->string('file_path');
            $table->string('file_type');
            $table->bigInteger('file_size'); // in bytes
            $table->text('description')->nullable();
            $table->string('license')->nullable();
            $table->boolean('watch')->default(false);
            $table->boolean('ignore_warnings')->default(false);
            $table->string('mime_type')->nullable();
            $table->string('storage_disk')->default('public'); // 'public', 'local', 's3', etc.
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('uploads');
    }
};
