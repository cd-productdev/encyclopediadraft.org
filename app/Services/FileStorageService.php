<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

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

        return $this->resolveDiskForPath($normalizedPath)->url($normalizedPath);
    }

    public function exists(string $path): bool
    {
        if ($this->isAbsoluteUrl($path)) {
            return true;
        }

        $normalizedPath = ltrim($path, '/');

        return $this->resolveDiskForPath($normalizedPath)->exists($normalizedPath);
    }

    public function delete(?string $path): void
    {
        if ($path === null || $path === '' || $this->isAbsoluteUrl($path)) {
            return;
        }

        $normalizedPath = ltrim($path, '/');
        $disk = $this->resolveDiskForPath($normalizedPath);

        if ($disk->exists($normalizedPath)) {
            $disk->delete($normalizedPath);
        }
    }

    protected function resolveDiskForPath(string $path)
    {
        $uploadsDisk = Storage::disk($this->disk());

        if ($uploadsDisk->exists($path)) {
            return $uploadsDisk;
        }

        if ($this->disk() !== 'public' && Storage::disk('public')->exists($path)) {
            return Storage::disk('public');
        }

        return $uploadsDisk;
    }

    protected function isAbsoluteUrl(string $path): bool
    {
        return str_starts_with($path, 'http://') || str_starts_with($path, 'https://');
    }
}
