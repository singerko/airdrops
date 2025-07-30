<?php
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
