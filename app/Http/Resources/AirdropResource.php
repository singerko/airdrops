<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AirdropResource extends JsonResource
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
            'description' => $this->description,
            'status' => $this->status,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'claim_start_date' => $this->claim_start_date,
            'claim_end_date' => $this->claim_end_date,
            'total_supply' => $this->total_supply,
            'token_symbol' => $this->token_symbol,
            'requirements' => $this->requirements,
            'claim_url' => $this->claim_url,
            'official_url' => $this->official_url,
            'image_url' => $this->image_url,
            'featured' => $this->featured,
            'verified' => $this->verified,
            'blockchain' => new BlockchainResource($this->whenLoaded('blockchain')),
            'project' => new ProjectResource($this->whenLoaded('project')),
            'categories' => CategoryResource::collection($this->whenLoaded('categories')),
            'phases' => PhaseResource::collection($this->whenLoaded('phases')),
            'views_count' => $this->views_count,
            'subscribers_count' => $this->subscribers_count,
            'average_rating' => $this->average_rating,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}