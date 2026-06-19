<?php

namespace Tests\Unit;

use App\Services\FileStorageService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class FileStorageServiceTest extends TestCase
{
    public function test_store_and_url_use_configured_uploads_disk(): void
    {
        config(['filesystems.uploads_disk' => 's3']);
        Storage::fake('s3');

        $service = app(FileStorageService::class);
        $file = UploadedFile::fake()->image('photo.jpg');
        $path = $service->store($file, 'infobox_images');

        Storage::disk('s3')->assertExists($path);
        $this->assertNotNull($service->url($path));
    }

    public function test_delete_removes_file_from_uploads_disk(): void
    {
        config(['filesystems.uploads_disk' => 's3']);
        Storage::fake('s3');

        $service = app(FileStorageService::class);
        $file = UploadedFile::fake()->image('photo.jpg');
        $path = $service->store($file, 'article_images');

        $service->delete($path);

        Storage::disk('s3')->assertMissing($path);
    }

    public function test_url_includes_configured_root_prefix(): void
    {
        config([
            'filesystems.uploads_disk' => 's3',
            'filesystems.disks.s3.url' => 'https://example-spaces.test',
            'filesystems.disks.s3.root' => 'draft',
        ]);

        $service = app(FileStorageService::class);

        $this->assertSame(
            'https://example-spaces.test/draft/infobox_images/photo.jpg',
            $service->url('infobox_images/photo.jpg')
        );
    }

    public function test_rewrite_content_urls_replaces_legacy_storage_paths(): void
    {
        config([
            'filesystems.uploads_disk' => 's3',
            'filesystems.disks.s3.url' => 'https://example-spaces.test',
            'filesystems.disks.s3.root' => 'draft',
            'app.url' => 'https://encyclopediadraft.org',
        ]);

        $service = app(FileStorageService::class);
        $content = '<img src="https://encyclopediadraft.org/storage/article_images/photo.jpg">';

        $updated = $service->rewriteContentUrls($content);

        $this->assertStringContainsString('https://example-spaces.test/draft/article_images/photo.jpg', $updated);
        $this->assertStringNotContainsString('/storage/', $updated);
    }

    public function test_rewrite_content_urls_fixes_missing_root_in_spaces_urls(): void
    {
        config([
            'filesystems.uploads_disk' => 's3',
            'filesystems.disks.s3.url' => 'https://example-spaces.test',
            'filesystems.disks.s3.root' => 'draft',
        ]);

        $service = app(FileStorageService::class);
        $content = '<img src="https://example-spaces.test/article_images/photo.jpg">';

        $updated = $service->rewriteContentUrls($content);

        $this->assertStringContainsString('https://example-spaces.test/draft/article_images/photo.jpg', $updated);
    }
}
