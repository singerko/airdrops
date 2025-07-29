<?php
// app/Http/Controllers/AirdropController.php

namespace App\Http\Controllers;

use App\Models\Airdrop;
use App\Models\Blockchain;
use App\Models\AirdropCategory;
use App\Models\Project;
use Illuminate\Http\Request;

class AirdropController extends Controller
{
    public function index(Request $request)
    {
        $query = Airdrop::published()
            ->with(['project', 'blockchain', 'phases'])
            ->withCount(['subscriptions', 'favorites']);

        // Search
        if ($request->search) {
            $query->search($request->search);
        }

        // Filter by blockchain
        if ($request->blockchain && is_array($request->blockchain)) {
            $query->byBlockchain($request->blockchain);
        }

        // Filter by status
        if ($request->status) {
            $query->where('status', $request->status);
        }

        // Filter by category
        if ($request->category) {
            $query->whereHas('project', function ($q) use ($request) {
                $q->whereHas('category', function ($cq) use ($request) {
                    $cq->where('slug', $request->category);
                });
            });
        }

        // Filter by estimated value
        if ($request->min_value) {
            $query->where('estimated_value', '>=', $request->min_value);
        }
        if ($request->max_value) {
            $query->where('estimated_value', '<=', $request->max_value);
        }

        // Date filters
        if ($request->start_date) {
            $query->where('starts_at', '>=', $request->start_date);
        }
        if ($request->end_date) {
            $query->where('ends_at', '<=', $request->end_date);
        }

        // Sorting
        $sortBy = $request->sort_by ?? 'latest';
        switch ($sortBy) {
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
                $query->whereNotNull('ends_at')
                      ->where('ends_at', '>', now())
                      ->orderBy('ends_at');
                break;
            case 'starting_soon':
                $query->where('status', 'upcoming')
                      ->whereNotNull('starts_at')
                      ->where('starts_at', '>', now())
                      ->orderBy('starts_at');
                break;
            default:
                $query->latest('published_at');
        }

        $airdrops = $query->paginate(12)->withQueryString();

        // Get filter options
        $blockchains = Blockchain::active()->ordered()->get();
        $categories = AirdropCategory::active()->ordered()->get();
        
        $statusOptions = [
            'upcoming' => 'Upcoming',
            'active' => 'Active',
            'ended' => 'Ended',
        ];

        return view('airdrops.index', compact(
            'airdrops',
            'blockchains',
            'categories',
            'statusOptions'
        ));
    }

    public function show($slug)
    {
        $airdrop = Airdrop::published()
            ->with([
                'project.category',
                'blockchain',
                'phases' => function ($query) {
                    $query->orderBy('sort_order');
                },
                'translations.language'
            ])
            ->where('slug', $slug)
            ->firstOrFail();

        // Increment view count
        $airdrop->incrementViews();

        // Get similar airdrops
        $similar_airdrops = Airdrop::published()
            ->where('id', '!=', $airdrop->id)
            ->where(function ($query) use ($airdrop) {
                $query->where('blockchain_id', $airdrop->blockchain_id)
                      ->orWhereHas('project', function ($q) use ($airdrop) {
                          $q->where('category_id', $airdrop->project->category_id);
                      });
            })
            ->with(['project', 'blockchain'])
            ->limit(4)
            ->get();

        // Check if user is subscribed (if authenticated)
        $isSubscribed = false;
        $isFavorited = false;
        
        if (auth()->check()) {
            $isSubscribed = $airdrop->subscriptions()
                ->where('user_id', auth()->id())
                ->exists();
            
            $isFavorited = $airdrop->favorites()
                ->where('user_id', auth()->id())
                ->exists();
        }

        return view('airdrops.show', compact(
            'airdrop',
            'similar_airdrops',
            'isSubscribed',
            'isFavorited'
        ));
    }

    public function subscribe(Request $request, Airdrop $airdrop)
    {
        $this->middleware('auth');

        $user = auth()->user();
        
        $subscription = $user->subscriptions()->firstOrCreate(
            ['airdrop_id' => $airdrop->id],
            [
                'email_notifications' => true,
                'push_notifications' => false,
                'notification_types' => ['new_phase', 'deadline_reminder', 'status_change'],
            ]
        );

        if ($subscription->wasRecentlyCreated) {
            return response()->json([
                'success' => true,
                'message' => 'Successfully subscribed to notifications.',
                'subscribed' => true,
            ]);
        }

        $subscription->delete();

        return response()->json([
            'success' => true,
            'message' => 'Unsubscribed from notifications.',
            'subscribed' => false,
        ]);
    }

    public function favorite(Request $request, Airdrop $airdrop)
    {
        $this->middleware('auth');

        $user = auth()->user();
        
        $favorite = $user->favorites()->where('airdrop_id', $airdrop->id)->first();

        if ($favorite) {
            $favorite->delete();
            return response()->json([
                'success' => true,
                'message' => 'Removed from favorites.',
                'favorited' => false,
            ]);
        }

        $user->favorites()->create(['airdrop_id' => $airdrop->id]);

        return response()->json([
            'success' => true,
            'message' => 'Added to favorites.',
            'favorited' => true,
        ]);
    }

    public function rate(Request $request, Airdrop $airdrop)
    {
        $this->middleware('auth');

        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
        ]);

        // For simplicity, we'll just update the airdrop's average rating
        // In production, you might want a separate ratings table
        $newRating = $request->rating;
        $currentRating = $airdrop->rating;
        $currentCount = $airdrop->rating_count;
        
        $totalRating = ($currentRating * $currentCount) + $newRating;
        $newCount = $currentCount + 1;
        $averageRating = $totalRating / $newCount;

        $airdrop->update([
            'rating' => round($averageRating, 2),
            'rating_count' => $newCount,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Rating submitted successfully.',
            'new_rating' => $airdrop->rating,
            'rating_count' => $airdrop->rating_count,
        ]);
    }
}
