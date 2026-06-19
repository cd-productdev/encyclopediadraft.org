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

    public function test_url_returns_absolute_urls_unchanged(): void
    {
        $service = app(FileStorageService::class);
        $url = 'https://cdn.example.com/infobox_images/test.jpg';

        $this->assertSame($url, $service->url($url));
    }
}
