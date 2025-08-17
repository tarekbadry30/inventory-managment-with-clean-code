<?php

namespace App\DTOs\Stock;

use App\Contracts\DTOInterface;

readonly class StockCreateDTO implements DTOInterface
{
    public function __construct(
        public readonly int $warehouseId,
        public readonly int $inventoryItemId,
        public readonly int $quantity,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            warehouseId: $data['warehouse_id'],
            inventoryItemId: $data['inventory_item_id'],
            quantity: $data['quantity'],
        );
    }

    public function toArray(): array
    {
        return [
            'warehouse_id' => $this->warehouseId,
            'inventory_item_id' => $this->inventoryItemId,
            'quantity' => $this->quantity,
        ];
    }
}
