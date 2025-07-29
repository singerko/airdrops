<?php
// database/seeders/BlockchainSeeder.php

namespace Database\Seeders;

use App\Models\Blockchain;
use Illuminate\Database\Seeder;

class BlockchainSeeder extends Seeder
{
    public function run()
    {
        $blockchains = [
            [
                'name' => 'Ethereum',
                'slug' => 'ethereum',
                'description' => 'The world\'s programmable blockchain',
                'website' => 'https://ethereum.org',
                'explorer_url' => 'https://etherscan.io',
                'token_standard' => 'ERC-20',
                'is_active' => true,
                'sort_order' => 1,
            ],
            [
                'name' => 'Solana',
                'slug' => 'solana',
                'description' => 'Fast, decentralized blockchain built for mass adoption',
                'website' => 'https://solana.com',
                'explorer_url' => 'https://explorer.solana.com',
                'token_standard' => 'SPL',
                'is_active' => true,
                'sort_order' => 2,
            ],
            [
                'name' => 'Cosmos',
                'slug' => 'cosmos',
                'description' => 'An ever-expanding ecosystem of interoperable blockchains',
                'website' => 'https://cosmos.network',
                'explorer_url' => 'https://www.mintscan.io',
                'token_standard' => 'Cosmos SDK',
                'is_active' => true,
                'sort_order' => 3,
            ],
            [
                'name' => 'Polygon',
                'slug' => 'polygon',
                'description' => 'Ethereum\'s leading blockchain scaling solution',
                'website' => 'https://polygon.technology',
                'explorer_url' => 'https://polygonscan.com',
                'token_standard' => 'ERC-20',
                'is_active' => true,
                'sort_order' => 4,
            ],
            [
                'name' => 'Binance Smart Chain',
                'slug' => 'bsc',
                'description' => 'Parallel blockchain to Binance Chain with smart contract functionality',
                'website' => 'https://www.bnbchain.org',
                'explorer_url' => 'https://bscscan.com',
                'token_standard' => 'BEP-20',
                'is_active' => true,
                'sort_order' => 5,
            ],
            [
                'name' => 'Arbitrum',
                'slug' => 'arbitrum',
                'description' => 'Optimistic rollup scaling solution for Ethereum',
                'website' => 'https://arbitrum.io',
                'explorer_url' => 'https://arbiscan.io',
                'token_standard' => 'ERC-20',
                'is_active' => true,
                'sort_order' => 6,
            ],
        ];

        foreach ($blockchains as $blockchain) {
            Blockchain::firstOrCreate(
                ['slug' => $blockchain['slug']],
                $blockchain
            );
        }

        $this->command->info('Blockchains seeded successfully.');
    }
}
