<?php
// database/seeders/SampleDataSeeder.php

namespace Database\Seeders;

use App\Models\Project;
use App\Models\Airdrop;
use App\Models\AirdropPhase;
use App\Models\Blockchain;
use App\Models\AirdropCategory;
use App\Models\User;
use Illuminate\Database\Seeder;

class SampleDataSeeder extends Seeder
{
    public function run()
    {
        $admin = User::where('role', 'admin')->first();
        $ethereum = Blockchain::where('slug', 'ethereum')->first();
        $solana = Blockchain::where('slug', 'solana')->first();
        $defiCategory = AirdropCategory::where('slug', 'defi')->first();
        $gamefiCategory = AirdropCategory::where('slug', 'gamefi')->first();

        // Sample projects
        $projects = [
            [
                'name' => 'DeFi Protocol X',
                'slug' => 'defi-protocol-x',
                'description' => 'Revolutionary DeFi protocol with yield farming and lending features.',
                'website' => 'https://defiprotocolx.io',
                'twitter' => 'https://twitter.com/defiprotocolx',
                'category_id' => $defiCategory->id,
                'is_verified' => true,
                'rating' => 4.5,
                'rating_count' => 120,
            ],
            [
                'name' => 'GameFi World',
                'slug' => 'gamefi-world',
                'description' => 'Play-to-earn gaming metaverse with NFT integration.',
                'website' => 'https://gamefiworld.io',
                'twitter' => 'https://twitter.com/gamefiworld',
                'discord' => 'https://discord.gg/gamefiworld',
                'category_id' => $gamefiCategory->id,
                'is_verified' => true,
                'rating' => 4.2,
                'rating_count' => 89,
            ],
        ];

        foreach ($projects as $projectData) {
            $project = Project::firstOrCreate(
                ['slug' => $projectData['slug']],
                $projectData
            );

            // Create sample airdrop for each project
            $airdrop = Airdrop::firstOrCreate(
                ['slug' => $projectData['slug'] . '-airdrop'],
                [
                    'title' => $project->name . ' Airdrop',
                    'project_id' => $project->id,
                    'blockchain_id' => $project->name === 'DeFi Protocol X' ? $ethereum->id : $solana->id,
                    'description' => 'Join our airdrop campaign and earn exclusive tokens!',
                    'requirements' => 'Follow us on Twitter, join Discord, and complete tasks.',
                    'reward_amount' => '1000',
                    'reward_token' => strtoupper(substr($project->slug, 0, 3)),
                    'estimated_value' => rand(50, 500),
                    'status' => 'active',
                    'is_featured' => rand(0, 1),
                    'priority' => rand(1, 10),
                    'starts_at' => now()->subDays(rand(1, 30)),
                    'ends_at' => now()->addDays(rand(30, 90)),
                    'published_at' => now(),
                    'created_by' => $admin->id,
                ]
            );

            // Create sample phases
            AirdropPhase::firstOrCreate(
                [
                    'airdrop_id' => $airdrop->id,
                    'name' => 'Season 1',
                ],
                [
                    'description' => 'First phase of the airdrop campaign',
                    'instructions' => 'Complete social media tasks',
                    'reward_amount' => '500',
                    'estimated_value' => $airdrop->estimated_value / 2,
                    'status' => 'active',
                    'starts_at' => $airdrop->starts_at,
                    'ends_at' => $airdrop->starts_at->addDays(30),
                    'sort_order' => 1,
                ]
            );
        }

        $this->command->info('Sample data seeded successfully.');
    }
}
