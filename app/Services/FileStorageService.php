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
            return $path;
        }

        $normalizedPath = ltrim($path, '/');

        if ($this->disk() !== 'public' && $this->safeExists(Storage::disk('public'), $normalizedPath)) {
            return Storage::disk('public')->url($normalizedPath);
        }

        return Storage::disk($this->disk())->url($normalizedPath);
    }

    public function exists(string $path): bool
    {
        if ($this->isAbsoluteUrl($path)) {
            return true;
        }

        $normalizedPath = ltrim($path, '/');

        return $this->safeExists(Storage::disk($this->disk()), $normalizedPath)
            || ($this->disk() !== 'public' && $this->safeExists(Storage::disk('public'), $normalizedPath));
    }

    public function delete(?string $path): void
    {
        if ($path === null || $path === '' || $this->isAbsoluteUrl($path)) {
            return;
        }

        $normalizedPath = ltrim($path, '/');

        if ($this->safeExists(Storage::disk($this->disk()), $normalizedPath)) {
            Storage::disk($this->disk())->delete($normalizedPath);

            return;
        }

        if ($this->disk() !== 'public' && $this->safeExists(Storage::disk('public'), $normalizedPath)) {
            Storage::disk('public')->delete($normalizedPath);
        }
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
