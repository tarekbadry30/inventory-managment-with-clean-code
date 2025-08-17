<?php

namespace App\DTOs\InventoryItem;

use App\Contracts\DTOInterface;

class InventoryItemsSearchDTO implements DTOInterface
{
    public function __construct(
        public readonly ?string $search = null,
        public readonly ?float $minPrice = null,
        public readonly ?float $maxPrice = null,
        public readonly ?int $warehouseId = null,
        public readonly int $perPage = 15,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            search: $data['search'] ?? null,
            minPrice: isset($data['min_price']) ? (float) $data['min_price'] : null,
            maxPrice: isset($data['max_price']) ? (float) $data['max_price'] : null,
            warehouseId: isset($data['warehouse_id']) ? (int) $data['warehouse_id'] : null,
            perPage: isset($data['per_page']) ? (int) $data['per_page'] : 15,
        );
    }
    public function toArray(): array
    {
        return [
            'search' => $this->search,
            'min_price' => $this->minPrice,
            'max_price' => $this->maxPrice,
            'warehouse_id' => $this->warehouseId,
            'per_page' => $this->perPage,
        ];
    }
}
