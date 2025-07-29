<?php
// database/seeders/LanguageSeeder.php

namespace Database\Seeders;

use App\Models\Language;
use Illuminate\Database\Seeder;

class LanguageSeeder extends Seeder
{
    public function run()
    {
        $languages = [
            [
                'code' => 'en',
                'name' => 'English',
                'native_name' => 'English',
                'is_default' => true,
                'is_active' => true,
            ],
            [
                'code' => 'sk',
                'name' => 'Slovak',
                'native_name' => 'Slovenčina',
                'is_default' => false,
                'is_active' => true,
            ],
            [
                'code' => 'de',
                'name' => 'German',
                'native_name' => 'Deutsch',
                'is_default' => false,
                'is_active' => true,
            ],
            [
                'code' => 'es',
                'name' => 'Spanish',
                'native_name' => 'Español',
                'is_default' => false,
                'is_active' => true,
            ],
            [
                'code' => 'fr',
                'name' => 'French',
                'native_name' => 'Français',
                'is_default' => false,
                'is_active' => true,
            ],
        ];

        foreach ($languages as $language) {
            Language::firstOrCreate(
                ['code' => $language['code']],
                $language
            );
        }

        $this->command->info('Languages seeded successfully.');
    }
}
