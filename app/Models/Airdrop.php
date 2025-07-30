<?php
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
