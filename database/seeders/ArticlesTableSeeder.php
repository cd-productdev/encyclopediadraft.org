<?php

namespace Database\Seeders;

use App\Models\Article;
use App\Models\User;
use Database\Seeders\Concerns\ImportsEncyclopediaData;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ArticlesTableSeeder extends Seeder
{
    use ImportsEncyclopediaData;
    use WithoutModelEvents;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $rows = $this->loadJsonRows('articles.json');

        $defaultCreatorId = User::query()
            ->where('role', 'admin')
            ->value('id') ?? User::query()->value('id');

        if ($defaultCreatorId === null) {
            $this->command?->error('No users found. Run UsersTableSeeder first.');

            return;
        }

        $validUserIds = User::withTrashed()->pluck('id')->all();

        Schema::disableForeignKeyConstraints();
        DB::table('articles')->truncate();

        $imported = 0;

        foreach ($rows as $row) {
            $title = trim((string) ($row['title'] ?? ''));

            if ($title === '') {
                continue;
            }

            $slug = trim((string) ($row['slug'] ?? ''));

            if ($slug === '') {
                continue;
            }

            $createdBy = $this->nullableInt($row['created_by'] ?? null);
            if ($createdBy === null || ! in_array($createdBy, $validUserIds, true)) {
                $createdBy = $defaultCreatorId;
            }

            $categoryId = $this->nullableInt($row['category_id'] ?? null);

            $reviewedBy = $this->nullableInt($row['reviewed_by'] ?? null);
            if ($reviewedBy !== null && ! in_array($reviewedBy, $validUserIds, true)) {
                $reviewedBy = null;
            }

            $deletedBy = $this->nullableInt($row['deleted_by'] ?? null);
            if ($deletedBy !== null && ! in_array($deletedBy, $validUserIds, true)) {
                $deletedBy = null;
            }

            Article::query()->create([
                'id' => (int) $row['id'],
                'title' => $title,
                'slug' => $slug,
                'summary' => $this->nullableString($row['summary'] ?? null),
                'category_id' => $categoryId,
                'content' => $this->nullableString($row['content'] ?? null),
                'references' => $this->decodeJsonField($row['references'] ?? null),
                'infobox_image' => $this->nullableString($row['infobox_image'] ?? null),
                'status' => $this->nullableString($row['status'] ?? null) ?? Article::STATUS_DRAFT,
                'submitted_at' => $this->nullableDate($row['submitted_at'] ?? null),
                'published_at' => $this->nullableDate($row['published_at'] ?? null),
                'reviewed_by' => $reviewedBy,
                'rejection_reason' => $this->decodeJsonField($row['rejection_reason'] ?? null),
                'draft_reason' => $this->nullableString($row['draft_reason'] ?? null),
                'show_lock_icon' => $this->nullableBool($row['show_lock_icon'] ?? null) ?? false,
                'created_by' => $createdBy,
                'created_at' => $this->nullableDate($row['created_at'] ?? null) ?? now(),
                'updated_at' => $this->nullableDate($row['updated_at'] ?? null) ?? now(),
                'deleted_at' => $this->nullableDate($row['deleted_at'] ?? null),
                'deleted_by' => $deletedBy,
            ]);

            $imported++;
        }

        $maxId = Article::withTrashed()->max('id') ?? 0;
        if (DB::getDriverName() === 'mysql') {
            DB::statement('ALTER TABLE articles AUTO_INCREMENT = '.($maxId + 1));
        }
        Schema::enableForeignKeyConstraints();

        $this->command?->info("Imported {$imported} articles from CSV data.");
        $this->command?->info('Existing articles were replaced.');
    }
}
