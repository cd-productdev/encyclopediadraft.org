<?php

namespace App\Console\Commands;

use App\Services\LocalToCloudStorageMigrationService;
use Illuminate\Console\Command;

class MigrateLocalStorageToS3 extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'storage:migrate-to-s3
                            {--dry-run : Preview files that would be migrated without uploading or deleting}
                            {--skip-delete : Upload to S3 but keep local copies}
                            {--skip-content : Do not rewrite /storage/ URLs inside article content}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Upload previously stored local public files to the configured S3/Spaces disk and remove local copies';

    public function __construct(protected LocalToCloudStorageMigrationService $migrationService)
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $dryRun = (bool) $this->option('dry-run');
        $deleteLocal = ! (bool) $this->option('skip-delete');
        $updateContent = ! (bool) $this->option('skip-content');

        $this->components->info(sprintf(
            'Migrating local public files to %s disk%s.',
            $this->migrationService->remoteDiskName(),
            $dryRun ? ' (dry run)' : ''
        ));

        $fileStats = $this->migrationService->migrateFiles(
            deleteLocal: $deleteLocal,
            dryRun: $dryRun
        );

        if ($fileStats['errors'] !== [] && $fileStats['uploaded'] === 0 && $fileStats['skipped'] === 0) {
            foreach ($fileStats['errors'] as $error) {
                $this->components->error($error);
            }

            return self::FAILURE;
        }

        $dbStats = ['articles_updated' => 0, 'uploads_updated' => 0];

        if ($updateContent) {
            $dbStats = $this->migrationService->updateDatabaseReferences($dryRun);
        }

        $this->newLine();
        $this->components->twoColumnDetail('Uploaded to remote', (string) $fileStats['uploaded']);
        $this->components->twoColumnDetail('Already on remote', (string) $fileStats['skipped']);
        $this->components->twoColumnDetail('Local files removed', (string) $fileStats['deleted']);
        $this->components->twoColumnDetail('Failed', (string) $fileStats['failed']);
        $this->components->twoColumnDetail('Articles content updated', (string) $dbStats['articles_updated']);
        $this->components->twoColumnDetail('Upload records updated', (string) $dbStats['uploads_updated']);

        foreach ($fileStats['errors'] as $error) {
            $this->components->warn($error);
        }

        if ($fileStats['failed'] > 0) {
            return self::FAILURE;
        }

        if ($dryRun) {
            $this->components->info('Dry run complete. Re-run without --dry-run to perform the migration.');
        } else {
            $this->components->info('Migration complete.');
        }

        return self::SUCCESS;
    }
}
