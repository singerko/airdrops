<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Airdrop;
use App\Models\User;
use App\Models\UserSubscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AnalyticsController extends Controller
{
    public function index(Request $request)
    {
        $period = $request->get('period', '30days');
        
        $stats = [
            'total_users' => User::count(),
            'new_users' => $this->getNewUsers($period),
            'active_users' => $this->getActiveUsers($period),
            'total_airdrops' => Airdrop::count(),
            'active_airdrops' => Airdrop::where('status', 'active')->count(),
            'total_subscriptions' => UserSubscription::count(),
            'new_subscriptions' => $this->getNewSubscriptions($period),
        ];

        $charts = [
            'user_growth' => $this->getUserGrowthData($period),
            'airdrop_views' => $this->getAirdropViewsData($period),
            'popular_blockchains' => $this->getPopularBlockchains(),
            'user_activity' => $this->getUserActivityData($period),
        ];

        return view('admin.analytics.index', compact('stats', 'charts', 'period'));
    }

    private function getNewUsers($period)
    {
        $date = $this->getPeriodStartDate($period);
        return User::where('created_at', '>=', $date)->count();
    }

    private function getActiveUsers($period)
    {
        $date = $this->getPeriodStartDate($period);
        return User::where('last_login_at', '>=', $date)->count();
    }

    private function getNewSubscriptions($period)
    {
        $date = $this->getPeriodStartDate($period);
        return UserSubscription::where('created_at', '>=', $date)->count();
    }

    private function getUserGrowthData($period)
    {
        $date = $this->getPeriodStartDate($period);
        
        return User::select(DB::raw('DATE(created_at) as date'), DB::raw('COUNT(*) as count'))
            ->where('created_at', '>=', $date)
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->map(function ($item) {
                return [
                    'date' => $item->date,
                    'count' => $item->count,
                ];
            });
    }

    private function getAirdropViewsData($period)
    {
        $date = $this->getPeriodStartDate($period);
        
        return Airdrop::select('name', 'views_count')
            ->where('created_at', '>=', $date)
            ->orderBy('views_count', 'desc')
            ->limit(10)
            ->get();
    }

    private function getPopularBlockchains()
    {
        return DB::table('airdrops')
            ->join('blockchains', 'airdrops.blockchain_id', '=', 'blockchains.id')
            ->select('blockchains.name', DB::raw('COUNT(airdrops.id) as count'))
            ->groupBy('blockchains.id', 'blockchains.name')
            ->orderBy('count', 'desc')
            ->limit(10)
            ->get();
    }

    private function getUserActivityData($period)
    {
        $date = $this->getPeriodStartDate($period);
        
        return [
            'subscriptions' => UserSubscription::where('created_at', '>=', $date)->count(),
            'favorites' => DB::table('user_favorites')->where('created_at', '>=', $date)->count(),
            'ratings' => DB::table('airdrop_ratings')->where('created_at', '>=', $date)->count(),
        ];
    }

    private function getPeriodStartDate($period)
    {
        return match($period) {
            '7days' => now()->subDays(7),
            '30days' => now()->subDays(30),
            '90days' => now()->subDays(90),
            '1year' => now()->subYear(),
            default => now()->subDays(30),
        };
    }
}