<?php

namespace App\DTOs\Stock;

use App\Contracts\DTOInterface;

readonly class StockSearchDTO implements DTOInterface
{
    public function __construct(
        public ?string $search = null,
        public ?int $warehouseId = null,
        public ?int $inventoryItemId = null,
        public ?int $minQuantity = null,
        public ?int $maxQuantity = null,
        public ?bool $lowStockOnly = null,
        public int $perPage = 15,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            search: $data['search'] ?? null,
            warehouseId: $data['warehouse_id'] ?? null,
            inventoryItemId: $data['inventory_item_id'] ?? null,
            minQuantity: $data['min_quantity'] ?? null,
            maxQuantity: $data['max_quantity'] ?? null,
            lowStockOnly: isset($data['low_stock_only']) ? (bool)$data['low_stock_only'] : null,
            perPage: min($data['per_page'] ?? 15, 100),
        );
    }

    public function toArray(): array
    {
        return [
            'search' => $this->search,
            'warehouse_id' => $this->warehouseId,
            'inventory_item_id' => $this->inventoryItemId,
            'min_quantity' => $this->minQuantity,
            'max_quantity' => $this->maxQuantity,
            'low_stock_only' => $this->lowStockOnly,
            'per_page' => $this->perPage,
        ];
    }
}
