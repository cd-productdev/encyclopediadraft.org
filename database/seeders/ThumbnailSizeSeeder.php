<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ThumbnailSizeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $thumbnailSizes = [
            [
                'name' => '120px',
                'size' => 120,
                'description' => 'Small thumbnail size',
                'is_default' => false,
                'is_active' => true,
                'sort_order' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => '150px',
                'size' => 150,
                'description' => 'Medium-small thumbnail size',
                'is_default' => false,
                'is_active' => true,
                'sort_order' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => '180px',
                'size' => 180,
                'description' => 'Medium thumbnail size',
                'is_default' => false,
                'is_active' => true,
                'sort_order' => 3,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => '200px',
                'size' => 200,
                'description' => 'Medium-large thumbnail size',
                'is_default' => false,
                'is_active' => true,
                'sort_order' => 4,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => '250px',
                'size' => 250,
                'description' => 'Large thumbnail size',
                'is_default' => false,
                'is_active' => true,
                'sort_order' => 5,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => '300px',
                'size' => 300,
                'description' => 'Extra large thumbnail size (Default)',
                'is_default' => true,
                'is_active' => true,
                'sort_order' => 6,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($thumbnailSizes as $thumbnailSize) {
            $existing = DB::table('thumbnail_sizes')->where('name', $thumbnailSize['name'])->first();

            if ($existing) {
                // Remove created_at when updating to preserve original creation time
                $updateData = $thumbnailSize;
                unset($updateData['created_at']);
                $updateData['updated_at'] = now();

                DB::table('thumbnail_sizes')
                    ->where('name', $thumbnailSize['name'])
                    ->update($updateData);
            } else {
                DB::table('thumbnail_sizes')->insert($thumbnailSize);
            }
        }

        $this->command->info('Thumbnail sizes seeded successfully!');
    }
}
