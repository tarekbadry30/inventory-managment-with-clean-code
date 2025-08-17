<?php

namespace App\DTOs\StockTransfer;

use App\Contracts\DTOInterface;

class StockTransferStoreDTO implements DTOInterface
{
    public function __construct(
        public readonly mixed $fromWarehouseId,
        public readonly int $toWarehouseId,
        public readonly int $inventoryItemId,
        public readonly int $quantity,
        public readonly int $createdBy,
        public readonly ?string $notes = null,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            fromWarehouseId: $data['from_warehouse_id'],
            toWarehouseId: $data['to_warehouse_id'],
            inventoryItemId: $data['inventory_item_id'],
            quantity: $data['quantity'],
            createdBy: $data['created_by'],
            notes: $data['notes'] ?? null,
        );
    }

    public function toArray(): array
    {
        return [
            'from_warehouse_id' => $this->fromWarehouseId,
            'to_warehouse_id' => $this->toWarehouseId,
            'inventory_item_id' => $this->inventoryItemId,
            'quantity' => $this->quantity,
            'created_by' => $this->createdBy,
            'notes' => $this->notes,
        ];
    }
}
