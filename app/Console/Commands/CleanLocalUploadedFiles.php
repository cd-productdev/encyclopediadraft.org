<?php

namespace App\Console\Commands;

use App\Services\LocalToCloudStorageMigrationService;
use Illuminate\Console\Command;

class CleanLocalUploadedFiles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'storage:clean-local-uploads
                            {--dry-run : Preview local files that would be deleted}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete local public upload files that already exist on the configured S3/Spaces disk';

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

        $this->components->info(sprintf(
            'Cleaning local files already present on %s disk%s.',
            $this->migrationService->remoteDiskName(),
            $dryRun ? ' (dry run)' : ''
        ));

        $stats = $this->migrationService->cleanLocalFiles($dryRun);

        if ($stats['errors'] !== [] && $stats['deleted'] === 0 && $stats['skipped'] === 0) {
            foreach ($stats['errors'] as $error) {
                $this->components->error($error);
            }

            return self::FAILURE;
        }

        $this->newLine();
        $this->components->twoColumnDetail('Local files removed', (string) $stats['deleted']);
        $this->components->twoColumnDetail('Skipped (not on remote)', (string) $stats['skipped']);
        $this->components->twoColumnDetail('Failed', (string) $stats['failed']);

        foreach ($stats['errors'] as $error) {
            $this->components->warn($error);
        }

        if ($stats['failed'] > 0) {
            return self::FAILURE;
        }

        if ($dryRun) {
            $this->components->info('Dry run complete. Re-run without --dry-run to delete local files.');
        } else {
            $this->components->info('Local cleanup complete.');
        }

        return self::SUCCESS;
    }
}
