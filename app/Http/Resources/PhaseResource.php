<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PhaseResource extends JsonResource
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
            'description' => $this->description,
            'requirements' => $this->requirements,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'allocation_percentage' => $this->allocation_percentage,
            'order' => $this->order,
        ];
    }
}