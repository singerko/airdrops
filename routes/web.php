<?php
// routes/web.php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\AirdropController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Auth\WalletAuthController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\AirdropController as AdminAirdropController;
use App\Http\Controllers\Admin\ProjectController as AdminProjectController;
use App\Http\Controllers\Admin\BlockchainController as AdminBlockchainController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Admin\CategoryController as AdminCategoryController;
use App\Http\Controllers\Admin\LanguageController as AdminLanguageController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Public Routes
Route::get('/', [HomeController::class, 'index'])->name('home');

// Airdrops
Route::prefix('airdrops')->name('airdrops.')->group(function () {
    Route::get('/', [AirdropController::class, 'index'])->name('index');
    Route::get('/{slug}', [AirdropController::class, 'show'])->name('show');
    
    // Authenticated routes
    Route::middleware('auth')->group(function () {
        Route::post('/{airdrop}/subscribe', [AirdropController::class, 'subscribe'])->name('subscribe');
        Route::post('/{airdrop}/favorite', [AirdropController::class, 'favorite'])->name('favorite');
        Route::post('/{airdrop}/rate', [AirdropController::class, 'rate'])->name('rate');
    });
});

// Projects
Route::prefix('projects')->name('projects.')->group(function () {
    Route::get('/', [ProjectController::class, 'index'])->name('index');
    Route::get('/{slug}', [ProjectController::class, 'show'])->name('show');
});

// Authentication Routes
Auth::routes(['verify' => true]);

// Wallet Authentication
Route::prefix('auth/wallet')->name('wallet.')->group(function () {
    Route::post('/nonce', [WalletAuthController::class, 'getNonce'])->name('nonce');
    Route::post('/connect', [WalletAuthController::class, 'connect'])->name('connect');
    Route::post('/disconnect', [WalletAuthController::class, 'disconnect'])->name('disconnect');
});

// Social Authentication
Route::prefix('auth')->group(function () {
    Route::get('/{provider}', [App\Http\Controllers\Auth\SocialController::class, 'redirect'])
        ->name('social.redirect')
        ->where('provider', 'google|facebook|twitter');
    
    Route::get('/{provider}/callback', [App\Http\Controllers\Auth\SocialController::class, 'callback'])
        ->name('social.callback')
        ->where('provider', 'google|facebook|twitter');
});

// User Profile
Route::middleware('auth')->prefix('profile')->name('profile.')->group(function () {
    Route::get('/', [ProfileController::class, 'index'])->name('index');
    Route::put('/', [ProfileController::class, 'update'])->name('update');
    Route::put('/password', [ProfileController::class, 'updatePassword'])->name('password.update');
    Route::put('/notifications', [ProfileController::class, 'updateNotifications'])->name('notifications.update');
    Route::put('/blockchains', [ProfileController::class, 'updateBlockchainPreferences'])->name('blockchains.update');
    Route::post('/wallets/{wallet}/primary', [ProfileController::class, 'setPrimaryWallet'])->name('wallets.primary');
    Route::delete('/wallets/{wallet}', [ProfileController::class, 'removeWallet'])->name('wallets.remove');
});

// Admin Routes
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    // Dashboard
    Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');
    
    // Airdrops Management
    Route::resource('airdrops', AdminAirdropController::class);
    Route::post('airdrops/{airdrop}/translate', [AdminAirdropController::class, 'translate'])->name('airdrops.translate');
    Route::post('airdrops/{airdrop}/publish', [AdminAirdropController::class, 'publish'])->name('airdrops.publish');
    Route::post('airdrops/{airdrop}/unpublish', [AdminAirdropController::class, 'unpublish'])->name('airdrops.unpublish');
    
    // Airdrop Phases
    Route::resource('airdrops.phases', App\Http\Controllers\Admin\AirdropPhaseController::class)
        ->except(['index', 'show']);
    
    // Projects Management
    Route::resource('projects', AdminProjectController::class);
    Route::post('projects/{project}/verify', [AdminProjectController::class, 'verify'])->name('projects.verify');
    Route::post('projects/{project}/unverify', [AdminProjectController::class, 'unverify'])->name('projects.unverify');
    
    // Blockchains Management
    Route::resource('blockchains', AdminBlockchainController::class);
    
    // Categories Management
    Route::resource('categories', AdminCategoryController::class);
    
    // Languages Management
    Route::resource('languages', AdminLanguageController::class);
    
    // Users Management
    Route::resource('users', AdminUserController::class);
    Route::post('users/{user}/ban', [AdminUserController::class, 'ban'])->name('users.ban');
    Route::post('users/{user}/unban', [AdminUserController::class, 'unban'])->name('users.unban');
    
    // System Settings
    Route::get('settings', [App\Http\Controllers\Admin\SettingsController::class, 'index'])->name('settings.index');
    Route::put('settings', [App\Http\Controllers\Admin\SettingsController::class, 'update'])->name('settings.update');
    
    // Analytics
    Route::get('analytics', [App\Http\Controllers\Admin\AnalyticsController::class, 'index'])->name('analytics.index');
    
    // Bulk Operations
    Route::post('bulk/airdrops', [AdminAirdropController::class, 'bulk'])->name('bulk.airdrops');
    Route::post('bulk/projects', [AdminProjectController::class, 'bulk'])->name('bulk.projects');
});

// API Routes for AJAX calls
Route::prefix('api/v1')->middleware('throttle:60,1')->group(function () {
    Route::get('airdrops/search', [App\Http\Controllers\Api\AirdropController::class, 'search']);
    Route::get('projects/search', [App\Http\Controllers\Api\ProjectController::class, 'search']);
    Route::get('blockchains', [App\Http\Controllers\Api\BlockchainController::class, 'index']);
});

// Pre skupinu routes ktoré vyžadujú prihlásenie
Route::middleware(['auth', 'force.password.change'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index']);
    // ďalšie chránené routes...
});

// Admin routes
Route::middleware(['auth', 'admin', 'force.password.change'])->prefix('admin')->group(function () {
    // admin routes...
});
