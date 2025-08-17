<?php

namespace App\Http\Resources\Stock;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StockBasicResource extends JsonResource
{
    use \App\Traits\ResourcePaginationTrait;
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'quantity' => $this->quantity,
            'is_low_stock' => (bool)$this->is_low_stock,
            'warehouse_name' => $this->warehouse->name,
            'item_name' => $this->inventoryItem->name,
            'item_sku' => $this->inventoryItem->sku,
            'updated_at' => $this->updated_at,
        ];
    }
}
