<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WalletResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'address' => $this->address,
            'blockchain' => $this->blockchain,
            'is_primary' => $this->is_primary,
            'verified_at' => $this->verified_at,
            'created_at' => $this->created_at,
        ];
    }
}