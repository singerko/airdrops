<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserSubscription extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'airdrop_id',
        'notification_types'
    ];

    protected $casts = [
        'notification_types' => 'array'
    ];

    const NOTIFICATION_NEW_PHASE = 'new_phase';
    const NOTIFICATION_UPDATE = 'update';
    const NOTIFICATION_ENDING_SOON = 'ending_soon';
    const NOTIFICATION_STARTING_SOON = 'starting_soon';

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function airdrop(): BelongsTo
    {
        return $this->belongsTo(Airdrop::class);
    }
}
