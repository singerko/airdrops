<?php
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
