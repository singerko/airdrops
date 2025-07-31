<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
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
            'name' => $this->name,
            'email' => $this->email,
            'role' => $this->role,
            'preferred_language' => $this->preferred_language,
            'notification_settings' => $this->notification_settings,
            'wallets' => WalletResource::collection($this->whenLoaded('wallets')),
            'subscriptions' => AirdropResource::collection($this->whenLoaded('subscriptions')),
            'favorites' => AirdropResource::collection($this->whenLoaded('favorites')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}