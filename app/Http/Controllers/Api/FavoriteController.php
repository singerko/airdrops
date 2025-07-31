<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\AirdropResource;
use App\Models\Airdrop;
use Illuminate\Http\Request;

class FavoriteController extends Controller
{
    public function index(Request $request)
    {
        $favorites = $request->user()
            ->favorites()
            ->with(['blockchain', 'project', 'categories'])
            ->paginate(20);

        return AirdropResource::collection($favorites);
    }

    public function favorite(Request $request, Airdrop $airdrop)
    {
        $user = $request->user();

        if (!$user->favorites()->where('airdrop_id', $airdrop->id)->exists()) {
            $user->favorites()->attach($airdrop);
        }

        return response()->json([
            'message' => 'Airdrop added to favorites',
            'is_favorited' => true,
        ]);
    }

    public function unfavorite(Request $request, Airdrop $airdrop)
    {
        $user = $request->user();
        $user->favorites()->detach($airdrop);

        return response()->json([
            'message' => 'Airdrop removed from favorites',
            'is_favorited' => false,
        ]);
    }
}