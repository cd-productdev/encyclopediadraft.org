<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FileSizeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $fileSizes = [
            [
                'name' => '320x240px',
                'width' => 320,
                'height' => 240,
                'description' => 'Small thumbnail size',
                'is_default' => false,
                'is_active' => true,
                'sort_order' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => '640x480px',
                'width' => 640,
                'height' => 480,
                'description' => 'Standard VGA size',
                'is_default' => false,
                'is_active' => true,
                'sort_order' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => '800x600px',
                'width' => 800,
                'height' => 600,
                'description' => 'SVGA size',
                'is_default' => false,
                'is_active' => true,
                'sort_order' => 3,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => '1024x768px',
                'width' => 1024,
                'height' => 768,
                'description' => 'XGA size (Default)',
                'is_default' => true,
                'is_active' => true,
                'sort_order' => 4,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => '1280x1024px',
                'width' => 1280,
                'height' => 1024,
                'description' => 'SXGA size',
                'is_default' => false,
                'is_active' => true,
                'sort_order' => 5,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => '2560x2048px',
                'width' => 2560,
                'height' => 2048,
                'description' => 'Large high-resolution size',
                'is_default' => false,
                'is_active' => true,
                'sort_order' => 6,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($fileSizes as $fileSize) {
            $existing = DB::table('file_sizes')->where('name', $fileSize['name'])->first();

            if ($existing) {
                // Remove created_at when updating to preserve original creation time
                $updateData = $fileSize;
                unset($updateData['created_at']);
                $updateData['updated_at'] = now();

                DB::table('file_sizes')
                    ->where('name', $fileSize['name'])
                    ->update($updateData);
            } else {
                DB::table('file_sizes')->insert($fileSize);
            }
        }

        $this->command->info('File sizes seeded successfully!');
    }
}
