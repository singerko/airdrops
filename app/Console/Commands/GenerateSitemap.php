<?php
// app/Console/Commands/GenerateSitemap.php

namespace App\Console\Commands;

use App\Models\Airdrop;
use App\Models\Project;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class GenerateSitemap extends Command
{
    protected $signature = 'sitemap:generate';
    protected $description = 'Generate XML sitemap for the website';

    public function handle()
    {
        $this->info('Generating sitemap...');
        
        $sitemap = $this->generateSitemapXml();
        
        Storage::disk('public')->put('sitemap.xml', $sitemap);
        
        $this->info('Sitemap generated successfully at storage/app/public/sitemap.xml');
    }

    private function generateSitemapXml()
    {
        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
        
        // Home page
        $xml .= $this->addUrl(route('home'), now(), 'daily', '1.0');
        
        // Airdrops index
        $xml .= $this->addUrl(route('airdrops.index'), now(), 'daily', '0.9');
        
        // Projects index
        $xml .= $this->addUrl(route('projects.index'), now(), 'daily', '0.8');
        
        // Individual airdrops
        Airdrop::published()->chunk(100, function ($airdrops) use (&$xml) {
            foreach ($airdrops as $airdrop) {
                $xml .= $this->addUrl(
                    route('airdrops.show', $airdrop->slug),
                    $airdrop->updated_at,
                    'weekly',
                    '0.7'
                );
            }
        });
        
        // Individual projects
        Project::active()->chunk(100, function ($projects) use (&$xml) {
            foreach ($projects as $project) {
                $xml .= $this->addUrl(
                    route('projects.show', $project->slug),
                    $project->updated_at,
                    'weekly',
                    '0.6'
                );
            }
        });
        
        $xml .= '</urlset>';
        
        return $xml;
    }

    private function addUrl($url, $lastmod, $changefreq, $priority)
    {
        return "  <url>\n" .
               "    <loc>{$url}</loc>\n" .
               "    <lastmod>{$lastmod->format('Y-m-d')}</lastmod>\n" .
               "    <changefreq>{$changefreq}</changefreq>\n" .
               "    <priority>{$priority}</priority>\n" .
               "  </url>\n";
    }
}
