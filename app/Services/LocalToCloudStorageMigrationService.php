<?php

namespace App\Services;

use App\Models\Article;
use App\Models\Upload;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use League\Flysystem\UnableToCheckFileExistence;
use Throwable;

class LocalToCloudStorageMigrationService
{
    /**
     * @var list<string>
     */
    protected array $uploadDirectories = [
        'infobox_images',
        'article_images',
        'images',
        'categories',
    ];

    public function __construct(protected FileStorageService $fileStorage) {}

    public function localDiskName(): string
    {
        return 'public';
    }

    public function remoteDiskName(): string
    {
        return $this->fileStorage->disk();
    }

    /**
     * @return array{uploaded: int, skipped: int, deleted: int, failed: int, errors: list<string>}
     */
    public function migrateFiles(bool $deleteLocal = true, bool $dryRun = false): array
    {
        $stats = [
            'uploaded' => 0,
            'skipped' => 0,
            'deleted' => 0,
            'failed' => 0,
            'errors' => [],
        ];

        if ($this->remoteDiskName() === $this->localDiskName()) {
            $stats['errors'][] = 'Remote uploads disk is the same as local public disk. Set FILESYSTEM_UPLOAD_DISK=s3 in .env.';

            return $stats;
        }

        $localDisk = Storage::disk($this->localDiskName());
        $remoteDisk = Storage::disk($this->remoteDiskName());

        foreach ($this->collectLocalFiles($localDisk) as $path) {
            try {
                if ($this->safeExists($remoteDisk, $path)) {
                    $stats['skipped']++;

                    if ($deleteLocal && $localDisk->exists($path)) {
                        if (! $dryRun) {
                            $localDisk->delete($path);
                        }

                        $stats['deleted']++;
                    }

                    continue;
                }

                if ($dryRun) {
                    $stats['uploaded']++;

                    continue;
                }

                $remoteDisk->writeStream(
                    $path,
                    $localDisk->readStream($path),
                    ['visibility' => 'public']
                );

                $stats['uploaded']++;

                if ($deleteLocal && $localDisk->exists($path)) {
                    $localDisk->delete($path);
                    $stats['deleted']++;
                }
            } catch (\Throwable $exception) {
                $stats['failed']++;
                $stats['errors'][] = "{$path}: {$exception->getMessage()}";
            }
        }

        return $stats;
    }

    /**
     * @return array{deleted: int, skipped: int, failed: int, errors: list<string>}
     */
    public function cleanLocalFiles(bool $dryRun = false): array
    {
        $stats = [
            'deleted' => 0,
            'skipped' => 0,
            'failed' => 0,
            'errors' => [],
        ];

        if ($this->remoteDiskName() === $this->localDiskName()) {
            $stats['errors'][] = 'Remote uploads disk is the same as local public disk. Set FILESYSTEM_UPLOAD_DISK=s3 in .env.';

            return $stats;
        }

        $localDisk = Storage::disk($this->localDiskName());
        $remoteDisk = Storage::disk($this->remoteDiskName());

        foreach ($this->collectLocalFiles($localDisk) as $path) {
            try {
                if (! $this->safeExists($remoteDisk, $path)) {
                    $stats['skipped']++;

                    continue;
                }

                if ($dryRun) {
                    $stats['deleted']++;

                    continue;
                }

                $localDisk->delete($path);
                $stats['deleted']++;
            } catch (\Throwable $exception) {
                $stats['failed']++;
                $stats['errors'][] = "{$path}: {$exception->getMessage()}";
            }
        }

        return $stats;
    }

    /**
     * @return array{articles_updated: int, uploads_updated: int}
     */
    public function updateDatabaseReferences(bool $dryRun = false): array
    {
        $stats = [
            'articles_updated' => 0,
            'uploads_updated' => 0,
        ];

        if ($dryRun) {
            $baseUrl = rtrim((string) config('filesystems.disks.'.$this->remoteDiskName().'.url', ''), '/');

            $stats['articles_updated'] = Article::query()
                ->where(function ($query) use ($baseUrl): void {
                    $query->where('content', 'like', '%/storage/%')
                        ->orWhere('infobox_image', 'like', '%/storage/%')
                        ->orWhere('infobox_image', 'like', 'storage/%');

                    if ($baseUrl !== '') {
                        $query->orWhere('content', 'like', '%'.$baseUrl.'/%');
                    }
                })
                ->count();

            $stats['uploads_updated'] = Upload::query()
                ->where('storage_disk', $this->localDiskName())
                ->count();

            return $stats;
        }

        Article::query()
            ->orderBy('id')
            ->chunkById(100, function ($articles) use (&$stats): void {
                foreach ($articles as $article) {
                    $updates = [];

                    $updatedContent = $this->fileStorage->rewriteContentUrls($article->content);

                    if ($updatedContent !== $article->content) {
                        $updates['content'] = $updatedContent;
                    }

                    $normalizedInfoboxImage = $this->fileStorage->normalizeStoredPath($article->infobox_image);

                    if ($normalizedInfoboxImage !== $article->infobox_image) {
                        $updates['infobox_image'] = $normalizedInfoboxImage;
                    }

                    if ($updates !== []) {
                        $article->update($updates);
                        $stats['articles_updated']++;
                    }
                }
            });

        $stats['uploads_updated'] = Upload::query()
            ->where('storage_disk', $this->localDiskName())
            ->update(['storage_disk' => $this->remoteDiskName()]);

        return $stats;
    }

    /**
     * @return list<string>
     */
    protected function collectLocalFiles(Filesystem $localDisk): array
    {
        $paths = [];

        foreach ($this->uploadDirectories as $directory) {
            if (! $localDisk->exists($directory)) {
                continue;
            }

            foreach ($localDisk->allFiles($directory) as $path) {
                if (! Str::endsWith($path, '.gitignore')) {
                    $paths[] = $path;
                }
            }
        }

        sort($paths);

        return array_values(array_unique($paths));
    }

    protected function safeExists(Filesystem $disk, string $path): bool
    {
        try {
            return $disk->exists($path);
        } catch (UnableToCheckFileExistence|Throwable) {
            return false;
        }
    }
}
