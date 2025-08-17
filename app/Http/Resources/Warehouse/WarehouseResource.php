<?php

namespace App\Http\Resources\Warehouse;

use Illuminate\Http\Request;
use App\Http\Resources\Stock\StockResource;
use App\Traits\ResourcePaginationTrait;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Warehouse\WarehouseBasicResource;

class WarehouseResource extends JsonResource
{
    use ResourcePaginationTrait;
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return array_merge(
            WarehouseBasicResource::make($this)->toArray($request),

            [
                'description' => $this->description,
                'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
                'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
                'total_items' => $this->whenLoaded('stocks', function () {
                    return $this->stocks->count();
                }),
                'total_stock_quantity' => $this->whenLoaded('stocks', function () {
                    return $this->stocks->sum('quantity');
                }),
                'inventory' => $this->whenLoaded('stocks', function () {
                    return StockResource::collection($this->stocks);
                }),
                'low_stock_items' => $this->whenLoaded('stocks', function () {
                    return $this->stocks->where('is_low_stock', true)->count();
                }),

            ],
        );
    }
}
