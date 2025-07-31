<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AirdropTranslation extends Model
{
    use HasFactory;

    protected $fillable = [
        'airdrop_id',
        'language_code',
        'field_name',
        'translated_value',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // Relationships
    public function airdrop()
    {
        return $this->belongsTo(Airdrop::class);
    }

    public function language()
    {
        return $this->belongsTo(Language::class, 'language_code', 'code');
    }
}
