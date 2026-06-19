<?php

namespace App\Services;

use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use League\Flysystem\UnableToCheckFileExistence;
use Throwable;

class FileStorageService
{
    public function disk(): string
    {
        return (string) config('filesystems.uploads_disk', 's3');
    }

    public function store(UploadedFile $file, string $directory): string
    {
        return $file->store($directory, $this->disk());
    }

    public function storeAs(UploadedFile $file, string $directory, string $filename): string
    {
        return $file->storeAs($directory, $filename, $this->disk());
    }

    public function url(?string $path): ?string
    {
        if ($path === null || $path === '') {
            return null;
        }

        if ($this->isAbsoluteUrl($path)) {
            return $this->normalizeAbsoluteUrl($path);
        }

        $normalizedPath = $this->normalizeStoragePath($path);

        if ($this->disk() !== 'public' && $this->safeExists(Storage::disk('public'), $normalizedPath)) {
            return Storage::disk('public')->url($normalizedPath);
        }

        return $this->buildPublicUrl($normalizedPath);
    }

    public function rewriteContentUrls(?string $content): ?string
    {
        if ($content === null || $content === '') {
            return $content;
        }

        $patterns = [
            '~https?://[^"\'\s<>]+/storage/([^"\'<>]+)~i',
            '~/storage/([^"\'<>]+)~',
        ];

        foreach ($patterns as $pattern) {
            $replaced = preg_replace_callback(
                $pattern,
                fn (array $matches): string => $this->buildPublicUrl(urldecode($matches[1])),
                $content
            );

            if ($replaced !== null) {
                $content = $replaced;
            }
        }

        return $this->fixMissingRootInPublicUrls($content);
    }

    public function exists(string $path): bool
    {
        if ($this->isAbsoluteUrl($path)) {
            return true;
        }

        $normalizedPath = $this->normalizeStoragePath($path);

        return $this->safeExists(Storage::disk($this->disk()), $normalizedPath)
            || ($this->disk() !== 'public' && $this->safeExists(Storage::disk('public'), $normalizedPath));
    }

    public function delete(?string $path): void
    {
        if ($path === null || $path === '' || $this->isAbsoluteUrl($path)) {
            return;
        }

        $normalizedPath = $this->normalizeStoragePath($path);

        if ($this->safeExists(Storage::disk($this->disk()), $normalizedPath)) {
            Storage::disk($this->disk())->delete($normalizedPath);

            return;
        }

        if ($this->disk() !== 'public' && $this->safeExists(Storage::disk('public'), $normalizedPath)) {
            Storage::disk('public')->delete($normalizedPath);
        }
    }

    public function buildPublicUrl(string $path): string
    {
        $normalizedPath = $this->normalizeStoragePath($path);
        $diskName = $this->disk();
        $diskConfig = config('filesystems.disks.'.$diskName, []);
        $baseUrl = rtrim((string) ($diskConfig['url'] ?? ''), '/');
        $root = trim((string) ($diskConfig['root'] ?? ''), '/');

        if ($baseUrl !== '') {
            $segments = array_filter([$root, $normalizedPath]);

            return $baseUrl.'/'.implode('/', $segments);
        }

        return Storage::disk($diskName)->url($normalizedPath);
    }

    protected function normalizeStoragePath(string $path): string
    {
        $normalizedPath = ltrim($path, '/');

        if (str_starts_with($normalizedPath, 'storage/')) {
            $normalizedPath = substr($normalizedPath, 8);
        }

        $root = trim((string) config('filesystems.disks.'.$this->disk().'.root', ''), '/');

        if ($root !== '' && str_starts_with($normalizedPath, $root.'/')) {
            $normalizedPath = substr($normalizedPath, strlen($root) + 1);
        }

        return $normalizedPath;
    }

    protected function normalizeAbsoluteUrl(string $url): string
    {
        if (preg_match('~/storage/([^"\'\s<>]+)~', $url, $matches) === 1) {
            return $this->buildPublicUrl(urldecode($matches[1]));
        }

        return $this->fixMissingRootInPublicUrls($url) ?? $url;
    }

    protected function fixMissingRootInPublicUrls(?string $content): ?string
    {
        if ($content === null || $content === '') {
            return $content;
        }

        $diskConfig = config('filesystems.disks.'.$this->disk(), []);
        $baseUrl = rtrim((string) ($diskConfig['url'] ?? ''), '/');
        $root = trim((string) ($diskConfig['root'] ?? ''), '/');

        if ($baseUrl === '' || $root === '') {
            return $content;
        }

        $directories = 'infobox_images|article_images|images|categories';
        $pattern = '~'.preg_quote($baseUrl, '~').'/((?!'.preg_quote($root, '~').'/)(?:'.$directories.')/[^"\'\s<>]+)~';

        $replaced = preg_replace_callback(
            $pattern,
            fn (array $matches): string => $baseUrl.'/'.$root.'/'.$matches[1],
            $content
        );

        return $replaced ?? $content;
    }

    protected function safeExists(Filesystem $disk, string $path): bool
    {
        try {
            return $disk->exists($path);
        } catch (UnableToCheckFileExistence|Throwable) {
            return false;
        }
    }

    protected function isAbsoluteUrl(string $path): bool
    {
        return str_starts_with($path, 'http://') || str_starts_with($path, 'https://');
    }
}
