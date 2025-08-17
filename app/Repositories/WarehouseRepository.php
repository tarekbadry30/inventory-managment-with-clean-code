<?php

namespace App\Repositories;

use App\Models\Warehouse;
use App\Contracts\DTOInterface;
use App\Contracts\WarehouseRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class WarehouseRepository implements WarehouseRepositoryInterface
{
    public function findById(int $id): ?Warehouse
    {
        return Warehouse::with('stocks')->findOrFail($id);
    }

    public function getAll(DTOInterface $searchDTO): LengthAwarePaginator
    {
        return Warehouse::when($searchDTO->search, function ($q) use ($searchDTO) {
            $q->where('name', 'like', "%{$searchDTO->search}%")
                ->orWhere('location', 'like', "%{$searchDTO->search}%");
        })->paginate($searchDTO->perPage);
    }

    public function create(array $data): Warehouse
    {
        return Warehouse::create($data);
    }

    public function update(int $id, array $data): bool
    {
        return Warehouse::where('id', $id)->update($data);
    }

    public function delete(int $id): bool
    {
        return Warehouse::destroy($id) > 0;
    }

    public function findByIdWithInventoryItems(int $warehouseId, DTOInterface $filters): Warehouse
    {
        // i can use DB query builder for more performance enhancement

        return Warehouse::with(['stocks.inventoryItem' => function ($stockQuery) use ($filters) {
            $search = $filters->search ?? null;

            $stockQuery->when($search, function ($query) use ($search) {
                $query->whereHas('inventoryItem', function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('sku', 'like', "%{$search}%");
                });
            })
                ->when(isset($filters->min_quantity), function ($query) use ($filters) {
                    $query->where('quantity', '>=', $filters->min_quantity);
                })

                ->when(isset($filters->low_stock_only) && $filters->low_stock_only, function ($query) {
                    $query->whereHas('inventoryItem', function ($q) {
                        $q->where('stocks.is_low_stock', true);
                    });
                });
        }])->findOrFail($warehouseId);
    }

    public function getWarehouseWithInventory(int $warehouseId, DTOInterface $filters): Warehouse
    {
        return $this->findByIdWithInventoryItems($warehouseId, $filters);
    }
}
