<?php
// database/seeders/CategorySeeder.php

namespace Database\Seeders;

use App\Models\AirdropCategory;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run()
    {
        $categories = [
            [
                'name' => 'DeFi',
                'slug' => 'defi',
                'description' => 'Decentralized Finance protocols and applications',
                'icon' => '💰',
                'color' => '#10B981',
                'is_active' => true,
                'sort_order' => 1,
            ],
            [
                'name' => 'GameFi',
                'slug' => 'gamefi',
                'description' => 'Gaming and NFT projects with play-to-earn mechanics',
                'icon' => '🎮',
                'color' => '#8B5CF6',
                'is_active' => true,
                'sort_order' => 2,
            ],
            [
                'name' => 'NFT',
                'slug' => 'nft',
                'description' => 'Non-Fungible Token platforms and marketplaces',
                'icon' => '🖼️',
                'color' => '#F59E0B',
                'is_active' => true,
                'sort_order' => 3,
            ],
            [
                'name' => 'Infrastructure',
                'slug' => 'infrastructure',
                'description' => 'Blockchain infrastructure and tooling projects',
                'icon' => '🏗️',
                'color' => '#6B7280',
                'is_active' => true,
                'sort_order' => 4,
            ],
            [
                'name' => 'Layer 2',
                'slug' => 'layer2',
                'description' => 'Layer 2 scaling solutions and rollups',
                'icon' => '⚡',
                'color' => '#3B82F6',
                'is_active' => true,
                'sort_order' => 5,
            ],
            [
                'name' => 'Social',
                'slug' => 'social',
                'description' => 'Decentralized social networks and communication',
                'icon' => '👥',
                'color' => '#EF4444',
                'is_active' => true,
                'sort_order' => 6,
            ],
        ];

        foreach ($categories as $category) {
            AirdropCategory::firstOrCreate(
                ['slug' => $category['slug']],
                $category
            );
        }

        $this->command->info('Categories seeded successfully.');
    }
}
