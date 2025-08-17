<?php

namespace App\Http\Resources\Stock;

use Illuminate\Http\Request;
use App\Traits\ResourcePaginationTrait;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Warehouse\WarehouseBasicResource;
use App\Http\Resources\InventoryItem\InventoryItemBasicResource;

class StockResource extends JsonResource
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
            'quantity' => $this->quantity,
            'is_low_stock' => (bool)$this->is_low_stock,
            'warehouse' => $this->whenLoaded('warehouse', function () {
                return WarehouseBasicResource::make($this->warehouse);
            }),
            'inventory_item' => $this->whenLoaded('inventoryItem', function () {
                return InventoryItemBasicResource::make($this->inventoryItem);
            }),
            'low_stock_threshold' => $this->whenLoaded('inventoryItem', function () {
                return $this->inventoryItem->low_stock_threshold;
            }),
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}
