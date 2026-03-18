<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class MakeMigrationWithSlug extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:migration:slug 
                            {name : The name of the migration}
                            {--table= : The table name}
                            {--create= : The table to be created}
                            {--path= : The location where the migration file should be created}
                            {--realpath : Indicate that any provided migration file paths are pre-resolved absolute paths}
                            {--fullpath : Output the full path of the migration}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new migration file with slug field automatically included';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // First, call the standard make:migration command
        $this->call('make:migration', [
            'name' => $this->argument('name'),
            '--table' => $this->option('table'),
            '--create' => $this->option('create'),
            '--path' => $this->option('path'),
            '--realpath' => $this->option('realpath'),
            '--fullpath' => $this->option('fullpath'),
        ]);

        // Wait a moment for file to be created
        usleep(500000); // 0.5 seconds

        // Find the most recent migration file
        $migrationsPath = $this->option('path')
            ? base_path($this->option('path'))
            : database_path('migrations');

        $files = File::glob($migrationsPath.'/*.php');

        if (empty($files)) {
            $this->error('Migration file not found.');

            return 1;
        }

        // Get the most recent file
        usort($files, function ($a, $b) {
            return filemtime($b) - filemtime($a);
        });

        $migrationFile = $files[0];
        $content = File::get($migrationFile);

        // Check if slug already exists in the migration
        if (str_contains($content, '->slug(') || str_contains($content, "'slug'") || str_contains($content, '"slug"')) {
            $this->info('Slug field already exists in migration.');

            return 0;
        }

        // Pattern to find the Schema::create or Schema::table block
        if (preg_match('/(Schema::(?:create|table)\([^)]+\)\s*->\s*function\s*\(Blueprint\s+\$table\)\s*\{[^}]*)/s', $content, $matches)) {
            $schemaBlock = $matches[1];

            // Find where to insert slug (after $table->id() if it exists, otherwise after the opening brace)
            if (preg_match('/\$table->id\(\);/', $schemaBlock, $idMatch)) {
                // Insert after id()
                $newSchemaBlock = str_replace(
                    '$table->id();',
                    "\$table->id();\n            \$table->slug(); // Auto-generated slug field",
                    $schemaBlock
                );
            } else {
                // Insert after opening brace
                $newSchemaBlock = preg_replace(
                    '/(function\s*\(Blueprint\s+\$table\)\s*\{)/',
                    "$1\n            \$table->slug(); // Auto-generated slug field",
                    $schemaBlock
                );
            }

            $content = str_replace($schemaBlock, $newSchemaBlock, $content);
        } else {
            // Fallback: simple string replacement
            if (str_contains($content, '$table->id();')) {
                $content = str_replace(
                    '$table->id();',
                    "\$table->id();\n            \$table->slug(); // Auto-generated slug field",
                    $content
                );
            } else {
                // Insert after function opening
                $content = preg_replace(
                    '/(function\s*\(Blueprint\s+\$table\)\s*\{)/',
                    "$1\n            \$table->slug(); // Auto-generated slug field",
                    $content
                );
            }
        }

        File::put($migrationFile, $content);

        $this->info('✓ Migration created with slug field automatically added!');
        $this->line('  File: '.basename($migrationFile));

        return 0;
    }
}
