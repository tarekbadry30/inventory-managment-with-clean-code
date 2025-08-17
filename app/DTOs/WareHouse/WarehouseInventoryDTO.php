<?php

namespace App\DTOs\WareHouse;

use App\Contracts\DTOInterface;

class WarehouseInventoryDTO implements DTOInterface
{
    public function __construct(
        public readonly ?string $search = null,
        public readonly int $perPage = 15,
    ) {}

    public static function fromArray(array $data): self
    {

        return new self(
            search: $data['search'] ?? null,
            perPage: isset($data['per_page']) ? (int) $data['per_page'] : 15,
        );
    }
    public function toArray(): array
    {
        return [
            'search' => $this->search,
            'per_page' => $this->perPage,
        ];
    }
}
