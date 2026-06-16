<?php

namespace Database\Seeders;

use App\Models\User;
use Database\Seeders\Concerns\ImportsEncyclopediaData;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class UsersTableSeeder extends Seeder
{
    use ImportsEncyclopediaData;
    use WithoutModelEvents;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $rows = $this->loadJsonRows('users.json');

        Schema::disableForeignKeyConstraints();
        DB::table('users')->truncate();

        foreach ($rows as $row) {
            $email = trim((string) ($row['email'] ?? ''));

            if ($email === '') {
                continue;
            }

            User::query()->create([
                'id' => (int) $row['id'],
                'name' => $this->displayName($row),
                'real_name' => $this->nullableString($row['real_name'] ?? null),
                'email' => $email,
                'role' => $this->normalizeRole($row['role'] ?? null),
                'password' => $this->nullableString($row['password'] ?? null) ?? 'password',
                'email_verified_at' => $this->nullableDate($row['email_verified_at'] ?? null),
                'last_login_at' => $this->nullableDate($row['last_login_at'] ?? null),
                'edit_count' => $this->nullableInt($row['edit_count'] ?? null) ?? 0,
                'language' => $this->nullableString($row['language'] ?? $row['langugae'] ?? null),
                'gender' => $this->nullableString($row['gender'] ?? null),
                'signature' => $this->nullableString($row['signature'] ?? null),
                'remember_token' => $this->nullableString($row['remember_token'] ?? null),
                'created_at' => $this->nullableDate($row['created_at'] ?? null) ?? now(),
                'updated_at' => $this->nullableDate($row['updated_at'] ?? null) ?? now(),
                'deleted_at' => $this->nullableDate($row['deleted_at'] ?? null),
            ]);
        }

        $maxId = User::withTrashed()->max('id') ?? 0;
        if (DB::getDriverName() === 'mysql') {
            DB::statement('ALTER TABLE users AUTO_INCREMENT = '.($maxId + 1));
        }
        Schema::enableForeignKeyConstraints();

        $this->command?->info('Imported '.count($rows).' users from CSV data.');
        $this->command?->info('Existing users were replaced. Default password: password');
    }
}
