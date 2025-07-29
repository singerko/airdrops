<?php
// routes/api.php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Public API Routes
Route::prefix('v1')->group(function () {
    // Public read-only endpoints
    Route::get('airdrops', [App\Http\Controllers\Api\AirdropController::class, 'index']);
    Route::get('airdrops/{slug}', [App\Http\Controllers\Api\AirdropController::class, 'show']);
    Route::get('projects', [App\Http\Controllers\Api\ProjectController::class, 'index']);
    Route::get('projects/{slug}', [App\Http\Controllers\Api\ProjectController::class, 'show']);
    Route::get('blockchains', [App\Http\Controllers\Api\BlockchainController::class, 'index']);
    Route::get('categories', [App\Http\Controllers\Api\CategoryController::class, 'index']);
    
    // Authenticated API routes
    Route::middleware(['auth:sanctum', 'throttle:100,1'])->group(function () {
        // User profile
        Route::get('profile', [App\Http\Controllers\Api\ProfileController::class, 'show']);
        Route::put('profile', [App\Http\Controllers\Api\ProfileController::class, 'update']);
        
        // User subscriptions
        Route::get('subscriptions', [App\Http\Controllers\Api\SubscriptionController::class, 'index']);
        Route::post('airdrops/{airdrop}/subscribe', [App\Http\Controllers\Api\SubscriptionController::class, 'subscribe']);
        Route::delete('airdrops/{airdrop}/subscribe', [App\Http\Controllers\Api\SubscriptionController::class, 'unsubscribe']);
        
        // User favorites
        Route::get('favorites', [App\Http\Controllers\Api\FavoriteController::class, 'index']);
        Route::post('airdrops/{airdrop}/favorite', [App\Http\Controllers\Api\FavoriteController::class, 'favorite']);
        Route::delete('airdrops/{airdrop}/favorite', [App\Http\Controllers\Api\FavoriteController::class, 'unfavorite']);
        
        // Notifications
        Route::get('notifications', [App\Http\Controllers\Api\NotificationController::class, 'index']);
        Route::post('notifications/{notification}/read', [App\Http\Controllers\Api\NotificationController::class, 'markAsRead']);
        Route::post('notifications/read-all', [App\Http\Controllers\Api\NotificationController::class, 'markAllAsRead']);
    });
});
