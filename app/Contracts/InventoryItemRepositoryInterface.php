<?php

namespace App\Contracts;

use App\Models\InventoryItem;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface InventoryItemRepositoryInterface
{
    public function findById(int $id): ?InventoryItem;
    public function findByItemIdAndWarehouseId(int $itemId, int $warehouseId): ?InventoryItem;

    public function findByIdWithRelations(int $id, array $relations = []): ?InventoryItem;

    public function findBySku(string $sku): ?InventoryItem;

    public function search(DTOInterface $searchDTO): LengthAwarePaginator;

    public function create(array $data): InventoryItem;

    public function update(int $id, array $data): bool;

    public function delete(int $id): bool;

    public function getAllWithStocks(): Collection;
}
