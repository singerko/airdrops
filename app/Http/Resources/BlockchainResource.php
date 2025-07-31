<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BlockchainResource extends JsonResource
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
            'slug' => $this->slug,
            'symbol' => $this->symbol,
            'chain_id' => $this->chain_id,
            'explorer_url' => $this->explorer_url,
            'rpc_url' => $this->rpc_url,
            'icon_url' => $this->icon_url,
            'is_testnet' => $this->is_testnet,
            'is_active' => $this->is_active,
        ];
    }
}