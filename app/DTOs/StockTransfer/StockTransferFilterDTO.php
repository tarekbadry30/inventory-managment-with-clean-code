<?php

namespace App\DTOs\StockTransfer;

use App\Contracts\DTOInterface;

class StockTransferFilterDTO implements DTOInterface
{
    public function __construct(
        public readonly ?string $status = null,
        public readonly ?int $from_warehouse_id = null,
        public readonly ?int $to_warehouse_id = null,
        public readonly ?int $inventory_item_id = null,
        public readonly ?string $search = null,
        public readonly int $perPage = 15
    ) {}

    public static function fromArray(array $data): self
    {

        return new self(
            status: $data['status'] ?? null,
            from_warehouse_id: $data['from_warehouse_id'] ?? null,
            to_warehouse_id: $data['to_warehouse_id'] ?? null,
            inventory_item_id: $data['inventory_item_id'] ?? null,
            search: $data['search'] ?? null,
            perPage: isset($data['per_page']) ? (int) $data['per_page'] : 15,
        );
    }
    public function toArray(): array
    {
        return [
            'status' => $this->status,
            'from_warehouse_id' => $this->from_warehouse_id,
            'to_warehouse_id' => $this->to_warehouse_id,
            'inventory_item_id' => $this->inventory_item_id,
            'search' => $this->search,
            'per_page' => $this->perPage,
        ];
    }
}
