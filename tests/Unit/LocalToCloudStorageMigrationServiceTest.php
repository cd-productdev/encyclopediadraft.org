<?php

namespace Tests\Unit;

use App\Services\LocalToCloudStorageMigrationService;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class LocalToCloudStorageMigrationServiceTest extends TestCase
{
    public function test_migrate_files_uploads_to_remote_and_deletes_local_copy(): void
    {
        config(['filesystems.uploads_disk' => 's3']);
        Storage::fake('public');
        Storage::fake('s3');

        Storage::disk('public')->put('infobox_images/photo.jpg', 'image-data');

        $service = app(LocalToCloudStorageMigrationService::class);
        $stats = $service->migrateFiles(deleteLocal: true, dryRun: false);

        $this->assertSame(1, $stats['uploaded']);
        $this->assertSame(1, $stats['deleted']);
        Storage::disk('s3')->assertExists('infobox_images/photo.jpg');
        Storage::disk('public')->assertMissing('infobox_images/photo.jpg');
    }

    public function test_migrate_files_skips_existing_remote_files_and_deletes_local_copy(): void
    {
        config(['filesystems.uploads_disk' => 's3']);
        Storage::fake('public');
        Storage::fake('s3');

        Storage::disk('public')->put('article_images/editor.jpg', 'image-data');
        Storage::disk('s3')->put('article_images/editor.jpg', 'image-data');

        $service = app(LocalToCloudStorageMigrationService::class);
        $stats = $service->migrateFiles(deleteLocal: true, dryRun: false);

        $this->assertSame(0, $stats['uploaded']);
        $this->assertSame(1, $stats['skipped']);
        $this->assertSame(1, $stats['deleted']);
        Storage::disk('public')->assertMissing('article_images/editor.jpg');
    }

    public function test_clean_local_files_only_deletes_files_present_on_remote(): void
    {
        config(['filesystems.uploads_disk' => 's3']);
        Storage::fake('public');
        Storage::fake('s3');

        Storage::disk('public')->put('images/local-only.jpg', 'local');
        Storage::disk('public')->put('images/shared.jpg', 'shared');
        Storage::disk('s3')->put('images/shared.jpg', 'shared');

        $service = app(LocalToCloudStorageMigrationService::class);
        $stats = $service->cleanLocalFiles(dryRun: false);

        $this->assertSame(1, $stats['deleted']);
        $this->assertSame(1, $stats['skipped']);
        Storage::disk('public')->assertExists('images/local-only.jpg');
        Storage::disk('public')->assertMissing('images/shared.jpg');
    }
}
