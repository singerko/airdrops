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

// app/Models/Blockchain.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Blockchain extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'logo',
        'website',
        'explorer_url',
        'token_standard',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function airdrops()
    {
        return $this->hasMany(Airdrop::class);
    }

    public function userWallets()
    {
        return $this->hasMany(UserWallet::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }
}

// app/Models/Project.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'logo',
        'website',
        'twitter',
        'discord',
        'telegram',
        'github',
        'category_id',
        'rating',
        'rating_count',
        'is_verified',
        'is_active',
    ];

    protected $casts = [
        'rating' => 'decimal:2',
        'is_verified' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function category()
    {
        return $this->belongsTo(AirdropCategory::class, 'category_id');
    }

    public function airdrops()
    {
        return $this->hasMany(Airdrop::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeVerified($query)
    {
        return $query->where('is_verified', true);
    }

    public function getLogoUrlAttribute()
    {
        return $this->logo ? asset('storage/' . $this->logo) : null;
    }

    public function getSocialLinksAttribute()
    {
        return array_filter([
            'website' => $this->website,
            'twitter' => $this->twitter,
            'discord' => $this->discord,
            'telegram' => $this->telegram,
            'github' => $this->github,
        ]);
    }
}

// app/Models/Airdrop.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Airdrop extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'project_id',
        'blockchain_id',
        'description',
        'requirements',
        'reward_amount',
        'reward_token',
        'estimated_value',
        'status',
        'is_featured',
        'priority',
        'social_links',
        'video_links',
        'featured_image',
        'views_count',
        'participants_count',
        'rating',
        'rating_count',
        'starts_at',
        'ends_at',
        'published_at',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'estimated_value' => 'decimal:2',
        'is_featured' => 'boolean',
        'rating' => 'decimal:2',
        'social_links' => 'array',
        'video_links' => 'array',
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'published_at' => 'datetime',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function blockchain()
    {
        return $this->belongsTo(Blockchain::class);
    }

    public function phases()
    {
        return $this->hasMany(AirdropPhase::class)->orderBy('sort_order');
    }

    public function translations()
    {
        return $this->hasMany(AirdropTranslation::class);
    }

    public function subscriptions()
    {
        return $this->hasMany(UserSubscription::class);
    }

    public function favorites()
    {
        return $this->hasMany(UserFavorite::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    // Scopes
    public function scopePublished($query)
    {
        return $query->whereNotNull('published_at')
                    ->where('published_at', '<=', now());
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeUpcoming($query)
    {
        return $query->where('status', 'upcoming');
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeEndingSoon($query, $days = 7)
    {
        return $query->where('status', 'active')
                    ->where('ends_at', '>', now())
                    ->where('ends_at', '<=', now()->addDays($days));
    }

    public function scopeStartingSoon($query, $days = 7)
    {
        return $query->where('status', 'upcoming')
                    ->where('starts_at', '>', now())
                    ->where('starts_at', '<=', now()->addDays($days));
    }

    public function scopeByBlockchain($query, $blockchainIds)
    {
        if (is_array($blockchainIds) && !empty($blockchainIds)) {
            return $query->whereIn('blockchain_id', $blockchainIds);
        }
        return $query;
    }

    public function scopeSearch($query, $search)
    {
        if ($search) {
            return $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhereHas('project', function ($pq) use ($search) {
                      $pq->where('name', 'like', "%{$search}%");
                  });
            });
        }
        return $query;
    }

    // Helper methods
    public function isActive()
    {
        return $this->status === 'active';
    }

    public function isUpcoming()
    {
        return $this->status === 'upcoming';
    }

    public function isEnded()
    {
        return $this->status === 'ended';
    }

    public function getDaysUntilStart()
    {
        if (!$this->starts_at || $this->starts_at <= now()) {
            return 0;
        }
        return now()->diffInDays($this->starts_at);
    }

    public function getDaysUntilEnd()
    {
        if (!$this->ends_at || $this->ends_at <= now()) {
            return 0;
        }
        return now()->diffInDays($this->ends_at);
    }

    public function getProgress()
    {
        if (!$this->starts_at || !$this->ends_at) {
            return 0;
        }
        
        $total = $this->starts_at->diffInDays($this->ends_at);
        $elapsed = $this->starts_at->diffInDays(now());
        
        if ($elapsed <= 0) return 0;
        if ($elapsed >= $total) return 100;
        
        return round(($elapsed / $total) * 100);
    }

    public function getFeaturedImageUrl()
    {
        return $this->featured_image ? asset('storage/' . $this->featured_image) : null;
    }

    public function incrementViews()
    {
        $this->increment('views_count');
    }

    public function getStatusBadgeClass()
    {
        return match($this->status) {
            'active' => 'bg-green-500',
            'upcoming' => 'bg-blue-500',
            'ended' => 'bg-gray-500',
            'cancelled' => 'bg-red-500',
            default => 'bg-gray-400',
        };
    }
}

// app/Models/AirdropPhase.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AirdropPhase extends Model
{
    use HasFactory;

    protected $fillable = [
        'airdrop_id',
        'name',
        'description',
        'instructions',
        'requirements',
        'reward_amount',
        'estimated_value',
        'status',
        'starts_at',
        'ends_at',
        'sort_order',
    ];

    protected $casts = [
        'requirements' => 'array',
        'estimated_value' => 'decimal:2',
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
    ];

    public function airdrop()
    {
        return $this->belongsTo(Airdrop::class);
    }

    public function isActive()
    {
        return $this->status === 'active';
    }

    public function isUpcoming()
    {
        return $this->status === 'upcoming';
    }

    public function isEnded()
    {
        return $this->status === 'ended';
    }

    public function getProgress()
    {
        if (!$this->starts_at || !$this->ends_at) {
            return 0;
        }
        
        $total = $this->starts_at->diffInDays($this->ends_at);
        $elapsed = $this->starts_at->diffInDays(now());
        
        if ($elapsed <= 0) return 0;
        if ($elapsed >= $total) return 100;
        
        return round(($elapsed / $total) * 100);
    }
}

// app/Models/AirdropCategory.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AirdropCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'icon',
        'color',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function projects()
    {
        return $this->hasMany(Project::class, 'category_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }
}

// app/Models/UserWallet.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserWallet extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'blockchain_id',
        'address',
        'wallet_type',
        'is_primary',
        'is_verified',
        'verified_at',
    ];

    protected $casts = [
        'is_primary' => 'boolean',
        'is_verified' => 'boolean',
        'verified_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function blockchain()
    {
        return $this->belongsTo(Blockchain::class);
    }

    public function markAsPrimary()
    {
        // Remove primary status from other wallets of the same blockchain
        static::where('user_id', $this->user_id)
              ->where('blockchain_id', $this->blockchain_id)
              ->where('id', '!=', $this->id)
              ->update(['is_primary' => false]);
        
        $this->update(['is_primary' => true]);
    }

    public function getShortAddressAttribute()
    {
        return substr($this->address, 0, 6) . '...' . substr($this->address, -4);
    }
}

// app/Models/Language.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Language extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'native_name',
        'is_default',
        'is_active',
    ];

    protected $casts = [
        'is_default' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function airdropTranslations()
    {
        return $this->hasMany(AirdropTranslation::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeDefault($query)
    {
        return $query->where('is_default', true);
    }

    public static function getDefault()
    {
        return static::default()->first() ?? static::where('code', 'en')->first();
    }
}
