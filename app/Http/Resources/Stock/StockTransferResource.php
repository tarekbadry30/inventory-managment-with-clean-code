<?php

namespace App\Http\Resources\Stock;

use Illuminate\Http\Request;
use App\Http\Resources\User\UserResource;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Warehouse\WarehouseBasicResource;
use App\Http\Resources\InventoryItem\InventoryItemBasicResource;

class StockTransferResource extends JsonResource
{
    use \App\Traits\ResourcePaginationTrait;
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
            'notes' => $this->notes,
            'status' => $this->status,
            'from_warehouse' => WarehouseBasicResource::make($this->fromWarehouse),
            'to_warehouse' => WarehouseBasicResource::make($this->toWarehouse),
            'inventory_item' => InventoryItemBasicResource::make($this->inventoryItem),
            'created_by' => UserResource::make($this->creator),
            'transferred_at' => $this->transferred_at?->format('Y-m-d H:i:s'),
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}
