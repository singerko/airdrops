<?php
// app/Http/Controllers/HomeController.php

namespace App\Http\Controllers;

use App\Models\Airdrop;
use App\Models\Project;
use App\Models\Blockchain;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        $stats = [
            'total_airdrops' => Airdrop::published()->count(),
            'active_airdrops' => Airdrop::published()->active()->count(),
            'total_projects' => Project::active()->count(),
            'total_blockchains' => Blockchain::active()->count(),
        ];

        $featured_airdrops = Airdrop::published()
            ->featured()
            ->with(['project', 'blockchain'])
            ->orderBy('priority', 'desc')
            ->limit(6)
            ->get();

        $latest_airdrops = Airdrop::published()
            ->with(['project', 'blockchain'])
            ->latest('published_at')
            ->limit(8)
            ->get();

        $ending_soon = Airdrop::published()
            ->endingSoon(7)
            ->with(['project', 'blockchain'])
            ->orderBy('ends_at')
            ->limit(6)
            ->get();

        $starting_soon = Airdrop::published()
            ->startingSoon(7)
            ->with(['project', 'blockchain'])
            ->orderBy('starts_at')
            ->limit(6)
            ->get();

        return view('home', compact(
            'stats',
            'featured_airdrops',
            'latest_airdrops',
            'ending_soon',
            'starting_soon'
        ));
    }
}
