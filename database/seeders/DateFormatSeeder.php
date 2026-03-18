<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DateFormatSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $dateFormats = [
            [
                'name' => 'No preference',
                'format' => null, // System default
                'description' => 'Use system default date format',
                'example' => 'System default',
                'locale' => 'en',
                'is_default' => true,
                'is_active' => true,
                'sort_order' => 1,
                'type' => 'datetime',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Time, Month Day, Year',
                'format' => 'H:i, F j, Y',
                'description' => 'Time followed by full month name, day, and year',
                'example' => '17:59, January 13, 2026',
                'locale' => 'en',
                'is_default' => false,
                'is_active' => true,
                'sort_order' => 2,
                'type' => 'datetime',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Time, Day Month Year',
                'format' => 'H:i, j F Y',
                'description' => 'Time followed by day, full month name, and year',
                'example' => '17:59, 13 January 2026',
                'locale' => 'en',
                'is_default' => false,
                'is_active' => true,
                'sort_order' => 3,
                'type' => 'datetime',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Time, Year Month Day',
                'format' => 'H:i, Y F j',
                'description' => 'Time followed by year, full month name, and day',
                'example' => '17:59, 2026 January 13',
                'locale' => 'en',
                'is_default' => false,
                'is_active' => true,
                'sort_order' => 4,
                'type' => 'datetime',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'ISO 8601',
                'format' => 'Y-m-d\TH:i:s',
                'description' => 'International standard date and time format (ISO 8601)',
                'example' => '2026-01-13T17:59:38',
                'locale' => 'en',
                'is_default' => false,
                'is_active' => true,
                'sort_order' => 5,
                'type' => 'datetime',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($dateFormats as $format) {
            $existing = DB::table('date_formats')->where('name', $format['name'])->first();

            if ($existing) {
                // Remove created_at when updating to preserve original creation time
                $updateData = $format;
                unset($updateData['created_at']);
                $updateData['updated_at'] = now();

                DB::table('date_formats')
                    ->where('name', $format['name'])
                    ->update($updateData);
            } else {
                DB::table('date_formats')->insert($format);
            }
        }

        $this->command->info('Date formats seeded successfully!');
    }
}
