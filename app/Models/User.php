<?php
// app/Models/User.php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'avatar',
        'country',
        'theme',
        'accent_color',
        'notification_settings',
        'preferred_blockchains',
        'force_password_change',
        'last_login_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'notification_settings' => 'array',
        'preferred_blockchains' => 'array',
        'force_password_change' => 'boolean',
        'last_login_at' => 'datetime',
    ];

    public function wallets()
    {
        return $this->hasMany(UserWallet::class);
    }

    public function subscriptions()
    {
        return $this->hasMany(UserSubscription::class);
    }

    public function favorites()
    {
        return $this->hasMany(UserFavorite::class);
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }

    public function socialLogins()
    {
        return $this->hasMany(SocialLogin::class);
    }

    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    public function primaryWallet($blockchainId = null)
    {
        $query = $this->wallets()->where('is_primary', true);
        
        if ($blockchainId) {
            $query->where('blockchain_id', $blockchainId);
        }
        
        return $query->first();
    }
}
