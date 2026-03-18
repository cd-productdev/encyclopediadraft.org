<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LanguageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $languages = [
            [
                'code' => 'en',
                'name' => 'English',
                'native_name' => 'English',
                'direction' => 'ltr',
                'is_active' => true,
                'is_default' => true,
                'sort_order' => 1,
                'flag' => '🇬🇧',
            ],
            [
                'code' => 'hi',
                'name' => 'Hindi',
                'native_name' => 'हिन्दी',
                'direction' => 'ltr',
                'is_active' => true,
                'is_default' => false,
                'sort_order' => 2,
                'flag' => '🇮🇳',
            ],
            [
                'code' => 'ur',
                'name' => 'Urdu',
                'native_name' => 'اردو',
                'direction' => 'rtl',
                'is_active' => true,
                'is_default' => false,
                'sort_order' => 3,
                'flag' => '🇵🇰',
            ],
            [
                'code' => 'ar',
                'name' => 'Arabic',
                'native_name' => 'العربية',
                'direction' => 'rtl',
                'is_active' => true,
                'is_default' => false,
                'sort_order' => 4,
                'flag' => '🇸🇦',
            ],
            [
                'code' => 'es',
                'name' => 'Spanish',
                'native_name' => 'Español',
                'direction' => 'ltr',
                'is_active' => true,
                'is_default' => false,
                'sort_order' => 5,
                'flag' => '🇪🇸',
            ],
            [
                'code' => 'fr',
                'name' => 'French',
                'native_name' => 'Français',
                'direction' => 'ltr',
                'is_active' => true,
                'is_default' => false,
                'sort_order' => 6,
                'flag' => '🇫🇷',
            ],
            [
                'code' => 'de',
                'name' => 'German',
                'native_name' => 'Deutsch',
                'direction' => 'ltr',
                'is_active' => true,
                'is_default' => false,
                'sort_order' => 7,
                'flag' => '🇩🇪',
            ],
            [
                'code' => 'zh',
                'name' => 'Chinese',
                'native_name' => '中文',
                'direction' => 'ltr',
                'is_active' => true,
                'is_default' => false,
                'sort_order' => 8,
                'flag' => '🇨🇳',
            ],
            [
                'code' => 'ja',
                'name' => 'Japanese',
                'native_name' => '日本語',
                'direction' => 'ltr',
                'is_active' => true,
                'is_default' => false,
                'sort_order' => 9,
                'flag' => '🇯🇵',
            ],
            [
                'code' => 'pt',
                'name' => 'Portuguese',
                'native_name' => 'Português',
                'direction' => 'ltr',
                'is_active' => true,
                'is_default' => false,
                'sort_order' => 10,
                'flag' => '🇵🇹',
            ],
            [
                'code' => 'ru',
                'name' => 'Russian',
                'native_name' => 'Русский',
                'direction' => 'ltr',
                'is_active' => true,
                'is_default' => false,
                'sort_order' => 11,
                'flag' => '🇷🇺',
            ],
            [
                'code' => 'bn',
                'name' => 'Bengali',
                'native_name' => 'বাংলা',
                'direction' => 'ltr',
                'is_active' => true,
                'is_default' => false,
                'sort_order' => 12,
                'flag' => '🇧🇩',
            ],
        ];

        foreach ($languages as $language) {
            $existing = DB::table('languages')->where('code', $language['code'])->first();

            if ($existing) {
                // Update existing, but preserve created_at
                $updateData = array_diff_key($language, array_flip(['created_at']));
                DB::table('languages')
                    ->where('code', $language['code'])
                    ->update($updateData);
            } else {
                DB::table('languages')->insert($language);
            }
        }
    }
}
