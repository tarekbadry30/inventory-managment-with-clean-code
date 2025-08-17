<?php

namespace App\Http\Resources\Warehouse;

use Illuminate\Http\Request;
use App\Traits\ResourcePaginationTrait;
use Illuminate\Http\Resources\Json\JsonResource;

class WarehouseBasicResource extends JsonResource
{
    use ResourcePaginationTrait;
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
            'location' => $this->location,
        ];
    }
}
