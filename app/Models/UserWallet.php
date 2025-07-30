<?php
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
