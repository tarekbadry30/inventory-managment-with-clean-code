<?php

namespace App\Services;

use App\Models\Warehouse;
use App\Contracts\DTOInterface;
use App\Contracts\WarehouseRepositoryInterface;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class WarehouseService
{
    public function __construct(
        private WarehouseRepositoryInterface $warehouseRepository,
        private CacheService $cacheService
    ) {}

    public function search(DTOInterface $searchDTO): mixed
    {
        return $this->warehouseRepository->getAll($searchDTO);
    }

    public function create(array $data): Warehouse
    {
        return $this->warehouseRepository->create($data);
    }

    public function findById(int $id): ?Warehouse
    {
        return $this->warehouseRepository->findById($id);
    }

    public function update(int $id, array $data): bool
    {
        $warehouse = $this->warehouseRepository->findById($id);

        if (!$warehouse) {
            throw new ModelNotFoundException('Warehouse not found');
        }

        $result = $this->warehouseRepository->update($id, $data);

        if ($result) {
            // Clear related cache
            $this->clearCachedInventory($id);
        }

        return $result;
    }

    public function destroy(int $id): bool
    {
        $warehouse = $this->warehouseRepository->findById($id);

        if (!$warehouse) {
            throw new ModelNotFoundException('Warehouse not found');
        }

        // Check if warehouse has stock before deletion
        if ($warehouse->stocks()->exists()) {
            throw new \InvalidArgumentException('Cannot delete warehouse with existing stock');
        }

        $result = $this->warehouseRepository->delete($id);

        if ($result) {
            $this->clearCachedInventory($id);
        }

        return $result;
    }
    public function clearCachedInventory(int $warehouseId): bool
    {
        return $this->cacheService->clearWarehouse($warehouseId);
    }


    public function findByIdWithInventoryItems(int $id, DTOInterface $filters): Warehouse
    {
        // cache aside pattern
        // https://www.geeksforgeeks.org/system-design/cache-aside-pattern/
        $warehouse = $this->cacheService->getWarehouseWithItems($id);
        if ($warehouse) {
            return $warehouse;
        }
        $warehouse = $this->warehouseRepository->findByIdWithInventoryItems($id, $filters); //->stocks;

        $this->cacheService->cacheWarehouseWithItems($id, $warehouse);

        return $warehouse;
    }
}
