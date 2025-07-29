<?php
// app/Http/Controllers/Admin/DashboardController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Airdrop;
use App\Models\Project;
use App\Models\User;
use App\Models\Blockchain;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_airdrops' => Airdrop::count(),
            'active_airdrops' => Airdrop::where('status', 'active')->count(),
            'total_projects' => Project::count(),
            'total_users' => User::where('role', 'user')->count(),
            'total_blockchains' => Blockchain::active()->count(),
        ];

        $recent_airdrops = Airdrop::with(['project', 'blockchain'])
            ->latest()
            ->limit(10)
            ->get();

        $popular_airdrops = Airdrop::with(['project', 'blockchain'])
            ->orderBy('views_count', 'desc')
            ->limit(10)
            ->get();

        return view('admin.dashboard', compact('stats', 'recent_airdrops', 'popular_airdrops'));
    }
}
