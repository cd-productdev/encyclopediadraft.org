<?php

namespace Database\Seeders\Concerns;

use Illuminate\Support\Carbon;

trait ImportsEncyclopediaData
{
    protected function dataPath(string $filename): string
    {
        $customPath = env('ENCYCLOPEDIA_IMPORT_PATH');

        if ($customPath !== null && is_file($customPath.'/'.$filename)) {
            return $customPath.'/'.$filename;
        }

        return database_path('data/ency/'.$filename);
    }

    /**
     * @return list<array<string, string|null>>
     */
    protected function loadJsonRows(string $filename): array
    {
        $path = $this->dataPath($filename);

        if (! is_file($path)) {
            throw new \RuntimeException("Import file not found: {$path}");
        }

        $rows = json_decode(file_get_contents($path), true);

        if (! is_array($rows)) {
            throw new \RuntimeException("Invalid JSON in import file: {$path}");
        }

        return $rows;
    }

    protected function nullableString(?string $value): ?string
    {
        $value = trim((string) $value);

        return $value === '' ? null : $value;
    }

    protected function nullableInt(?string $value): ?int
    {
        $value = trim((string) $value);

        if ($value === '') {
            return null;
        }

        return (int) $value;
    }

    protected function nullableBool(?string $value): ?bool
    {
        $value = trim((string) $value);

        if ($value === '') {
            return null;
        }

        return in_array(strtolower($value), ['1', 'true', 'yes'], true);
    }

    protected function nullableDate(?string $value): ?Carbon
    {
        $value = trim((string) $value);

        if ($value === '') {
            return null;
        }

        return Carbon::parse($value);
    }

    protected function normalizeRole(?string $role): string
    {
        return match (strtolower(trim((string) $role))) {
            'administrator', 'admin' => 'admin',
            'moderator' => 'moderator',
            default => 'user',
        };
    }

    /**
     * @return array<int, mixed>|null
     */
    protected function decodeJsonField(?string $value): ?array
    {
        $value = trim((string) $value);

        if ($value === '') {
            return null;
        }

        $decoded = json_decode($value, true);

        return is_array($decoded) ? $decoded : null;
    }

    protected function displayName(array $row): string
    {
        $name = trim((string) ($row['name'] ?? ''));

        if ($name !== '') {
            return $name;
        }

        $email = trim((string) ($row['email'] ?? ''));

        if ($email !== '' && str_contains($email, '@')) {
            return explode('@', $email)[0];
        }

        return 'User '.$row['id'];
    }
}
