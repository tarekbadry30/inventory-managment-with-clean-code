<?php

namespace App\Contracts;

use App\Models\Warehouse;
use Illuminate\Database\Eloquent\Collection;
use App\DTOs\WareHouse\WarehouseInventoryDTO;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface WarehouseRepositoryInterface
{
    public function findById(int $id): ?Warehouse;

    public function findByIdWithInventoryItems(int $id, DTOInterface $filters): ?Warehouse;

    public function getAll(DTOInterface $searchDTO): LengthAwarePaginator;

    public function create(array $data): Warehouse;

    public function update(int $id, array $data): bool;

    public function delete(int $id): bool;
}
