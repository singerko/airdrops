<?php
// app/Http/Controllers/Api/AirdropController.php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Airdrop;
use Illuminate\Http\Request;

class AirdropController extends Controller
{
    public function index(Request $request)
    {
        $query = Airdrop::published()
            ->with(['project', 'blockchain'])
            ->withCount(['subscriptions', 'favorites']);

        // Apply filters
        if ($request->search) {
            $query->search($request->search);
        }

        if ($request->blockchain) {
            $blockchainIds = is_array($request->blockchain) 
                ? $request->blockchain 
                : explode(',', $request->blockchain);
            $query->byBlockchain($blockchainIds);
        }

        if ($request->status) {
            $query->where('status', $request->status);
        }

        // Sorting
        switch ($request->sort_by) {
            case 'popular':
                $query->orderBy('views_count', 'desc');
                break;
            case 'rating':
                $query->orderBy('rating', 'desc');
                break;
            case 'value':
                $query->orderBy('estimated_value', 'desc');
                break;
            case 'ending_soon':
                $query->endingSoon()->orderBy('ends_at');
                break;
            default:
                $query->latest('published_at');
        }

        $airdrops = $query->paginate($request->per_page ?? 12);

        return response()->json([
            'data' => $airdrops->items(),
            'meta' => [
                'current_page' => $airdrops->currentPage(),
                'per_page' => $airdrops->perPage(),
                'total' => $airdrops->total(),
                'last_page' => $airdrops->lastPage(),
            ],
        ]);
    }

    public function show($slug)
    {
        $airdrop = Airdrop::published()
            ->with(['project', 'blockchain', 'phases'])
            ->where('slug', $slug)
            ->firstOrFail();

        return response()->json($airdrop);
    }

    public function search(Request $request)
    {
        $query = $request->get('q');
        
        if (strlen($query) < 2) {
            return response()->json([]);
        }

        $airdrops = Airdrop::published()
            ->with(['project', 'blockchain'])
            ->search($query)
            ->limit(10)
            ->get();

        return response()->json($airdrops);
    }
}
