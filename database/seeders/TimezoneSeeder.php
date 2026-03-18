<?php

namespace Database\Seeders;

use DateTime;
use DateTimeZone;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TimezoneSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all timezone identifiers
        $timezoneIdentifiers = timezone_identifiers_list();

        $timezones = [];
        $sortOrder = 1;

        // Common timezones to set as default or prioritize
        $commonTimezones = [
            'UTC' => ['is_default' => true, 'sort_order' => 1],
            'America/New_York' => ['is_default' => false, 'sort_order' => 2],
            'America/Los_Angeles' => ['is_default' => false, 'sort_order' => 3],
            'Europe/London' => ['is_default' => false, 'sort_order' => 4],
            'Asia/Karachi' => ['is_default' => false, 'sort_order' => 5],
            'Asia/Dubai' => ['is_default' => false, 'sort_order' => 6],
            'Asia/Kolkata' => ['is_default' => false, 'sort_order' => 7],
            'Asia/Tokyo' => ['is_default' => false, 'sort_order' => 8],
            'Australia/Sydney' => ['is_default' => false, 'sort_order' => 9],
        ];

        foreach ($timezoneIdentifiers as $timezoneName) {
            try {
                $timezone = new DateTimeZone($timezoneName);
                $now = new DateTime('now', $timezone);
                $utc = new DateTime('now', new DateTimeZone('UTC'));

                // Calculate offset
                $offset = $timezone->getOffset($utc);
                $offsetHours = $offset / 3600;
                $offsetString = sprintf('%+03d:%02d', floor($offsetHours), abs(($offsetHours - floor($offsetHours)) * 60));

                // Get abbreviation
                $abbreviation = $now->format('T');

                // Extract region and country
                $parts = explode('/', $timezoneName);
                $region = $parts[0] ?? null;
                $country = null;

                // Try to extract country from timezone name
                if (isset($parts[1])) {
                    // Some timezones have country info in the name
                    $location = $parts[1];
                }

                // Generate display name
                $displayName = str_replace('_', ' ', $timezoneName);
                $displayName = str_replace('/', ' - ', $displayName);

                // Check if it's a common timezone
                $isDefault = isset($commonTimezones[$timezoneName]) ? $commonTimezones[$timezoneName]['is_default'] : false;
                $customSortOrder = isset($commonTimezones[$timezoneName]) ? $commonTimezones[$timezoneName]['sort_order'] : null;

                $timezones[] = [
                    'name' => $timezoneName,
                    'display_name' => $displayName,
                    'abbreviation' => $abbreviation,
                    'offset' => $offsetString,
                    'offset_hours' => round($offsetHours, 2),
                    'region' => $region,
                    'country' => $country,
                    'is_active' => true,
                    'sort_order' => $customSortOrder ?? ($sortOrder + 100), // Common ones first, then others
                    'is_default' => $isDefault,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];

                if ($customSortOrder === null) {
                    $sortOrder++;
                }
            } catch (\Exception $e) {
                // Skip invalid timezones
                continue;
            }
        }

        // Sort by sort_order
        usort($timezones, function ($a, $b) {
            return $a['sort_order'] <=> $b['sort_order'];
        });

        // Insert timezones
        foreach ($timezones as $timezone) {
            $existing = DB::table('timezones')->where('name', $timezone['name'])->first();

            if ($existing) {
                // Remove created_at when updating to preserve original creation time
                $updateData = $timezone;
                unset($updateData['created_at']);
                $updateData['updated_at'] = now();

                DB::table('timezones')
                    ->where('name', $timezone['name'])
                    ->update($updateData);
            } else {
                DB::table('timezones')->insert($timezone);
            }
        }

        $this->command->info('Timezones seeded successfully! Total: '.count($timezones));
    }
}
