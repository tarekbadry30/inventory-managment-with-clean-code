<?php

namespace App\Contracts;

use App\Models\Stock;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface StockRepositoryInterface
{
    public function findById(int $id, array $relations = []): ?Stock;

    public function search(DTOInterface $searchDTO): LengthAwarePaginator;

    public function create(array $data): Stock;

    public function update(int $id, array $data): bool;

    public function delete(int $id): bool;

    public function findByWarehouseAndItem(int $warehouseId, int $itemId): ?Stock;

    public function findOrCreateStock(int $warehouseId, int $itemId): Stock;

    public function updateQuantity(int $id, int $quantity): bool;

    public function getStockByWarehouse(int $warehouseId): Collection;
}
