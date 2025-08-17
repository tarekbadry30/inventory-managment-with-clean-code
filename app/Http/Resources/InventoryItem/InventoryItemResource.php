<?php

namespace App\Http\Resources\InventoryItem;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class InventoryItemResource extends JsonResource
{
    use \App\Traits\ResourcePaginationTrait;
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return array_merge(
            InventoryItemBasicResource::make($this)->toArray($request),
            [
                'total_stock_quantity' => $this->whenLoaded('stocks', function () {
                    return $this->stocks->sum('quantity');
                }),
                'inventory_stocks' => $this->whenLoaded('stocks', function () {
                    return $this->stocks->map(function ($stock) {
                        return [
                            'warehouse_id' => $stock->warehouse->id,
                            'warehouse_name' => $stock->warehouse->name,
                            'warehouse_location' => $stock->warehouse->location,
                            'quantity' => $stock->quantity,
                            'is_low_stock' => $stock->is_low_stock,
                        ];
                    });
                }),
            ]
        );
    }
}
