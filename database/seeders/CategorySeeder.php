<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Biography',
                'slug' => 'Biography',
                'description' => 'Articles about individuals, people, and personalities',
                'color' => '#3B82F6', // Blue
                'is_active' => true,
                'sort_order' => 1,
            ],
            [
                'name' => 'Company',
                'slug' => 'Company',
                'description' => 'Articles about companies, corporations, and businesses',
                'color' => '#10B981', // Green
                'is_active' => true,
                'sort_order' => 2,
            ],
            [
                'name' => 'Website',
                'slug' => 'Website',
                'description' => 'Articles about politicians, political figures, and government officials',
                'color' => '#EF4444', // Red
                'is_active' => true,
                'sort_order' => 3,
            ],
            [
                'name' => 'Musician',
                'slug' => 'Musician',
                'description' => 'Articles about organizations, institutions, and groups',
                'color' => '#8B5CF6', // Purple
                'is_active' => true,
                'sort_order' => 4,
            ],
            [
                'name' => 'Album',
                'slug' => 'Album',
                'description' => 'Articles about locations, cities, countries, and geographical places',
                'color' => '#F59E0B', // Amber
                'is_active' => true,
                'sort_order' => 5,
            ],
            [
                'name' => 'Tv Series',
                'slug' => 'tv-series',
                'description' => 'Articles about events, occasions, and happenings',
                'color' => '#EC4899', // Pink
                'is_active' => true,
                'sort_order' => 6,
            ],
            [
                'name' => 'Local',
                'slug' => 'local',
                'description' => 'Articles about products, goods, and items',
                'color' => '#06B6D4', // Cyan
                'is_active' => true,
                'sort_order' => 7,
            ],
            [
                'name' => 'Games',
                'slug' => 'games',
                'description' => 'Articles about technology, software, and innovations',
                'color' => '#6366F1', // Indigo
                'is_active' => true,
                'sort_order' => 8,
            ],
            [
                'name' => 'Fictional Character',
                'slug' => 'fictional-character',
                'description' => 'Articles about entertainment, media, and cultural content',
                'color' => '#F97316', // Orange
                'is_active' => true,
                'sort_order' => 9,
            ],
            [
                'name' => 'Sports',
                'slug' => 'sports',
                'description' => 'Articles about sports, athletes, and sporting events',
                'color' => '#14B8A6', // Teal
                'is_active' => true,
                'sort_order' => 10,
            ],
            [
                'name' => 'Podcast',
                'slug' => 'Podcast',
                'description' => 'Articles about scientific topics, research, and discoveries',
                'color' => '#84CC16', // Lime
                'is_active' => true,
                'sort_order' => 11,
            ],
            [
                'name' => 'History',
                'slug' => 'history',
                'description' => 'Articles about historical events, periods, and figures',
                'color' => '#78716C', // Stone
                'is_active' => true,
                'sort_order' => 12,
            ],
            [
                'name' => 'Education',
                'slug' => 'education',
                'description' => 'Articles about educational institutions, programs, and topics',
                'color' => '#0EA5E9', // Sky
                'is_active' => true,
                'sort_order' => 13,
            ],
            [
                'name' => 'Health',
                'slug' => 'health',
                'description' => 'Articles about health, medicine, and wellness',
                'color' => '#22C55E', // Green
                'is_active' => true,
                'sort_order' => 14,
            ],
            [
                'name' => 'Culture',
                'slug' => 'culture',
                'description' => 'Articles about culture, traditions, and customs',
                'color' => '#A855F7', // Purple
                'is_active' => true,
                'sort_order' => 15,
            ],
        ];

        foreach ($categories as $category) {
            Category::updateOrCreate(
                ['slug' => $category['slug']],
                $category
            );
        }

        $this->command->info('Categories seeded successfully!');
    }
}
