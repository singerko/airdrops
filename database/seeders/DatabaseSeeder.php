<?php
// database/seeders/DatabaseSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        $this->call([
            LanguageSeeder::class,
            AdminSeeder::class,
            BlockchainSeeder::class,
            CategorySeeder::class,
        ]);

        // Only add sample data in development
        if (app()->environment('local')) {
            $this->call([
                SampleDataSeeder::class,
            ]);
        }
    }
}
