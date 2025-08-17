<?php

namespace App\Repositories;

use App\Models\Stock;
use App\Contracts\DTOInterface;
use App\Contracts\StockRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class StockRepository implements StockRepositoryInterface
{
    public function findById(int $id, array $relations = []): ?Stock
    {
        return Stock::with($relations)->find($id);
    }

    public function search(DTOInterface $searchDTO): LengthAwarePaginator
    {
        $query = Stock::with(['warehouse', 'inventoryItem']);

        // Search by item name or warehouse name
        if ($searchDTO->search) {
            $query->where(function ($q) use ($searchDTO) {
                $q->whereHas('inventoryItem', function ($itemQuery) use ($searchDTO) {
                    $itemQuery->where('name', 'like', "%{$searchDTO->search}%")
                        ->orWhere('sku', 'like', "%{$searchDTO->search}%");
                })
                    ->orWhereHas('warehouse', function ($warehouseQuery) use ($searchDTO) {
                        $warehouseQuery->where('name', 'like', "%{$searchDTO->search}%")
                            ->orWhere('location', 'like', "%{$searchDTO->search}%");
                    });
            });
        }

        // Filter by warehouse
        if ($searchDTO->warehouseId) {
            $query->where('warehouse_id', $searchDTO->warehouseId);
        }

        // Filter by inventory item
        if ($searchDTO->inventoryItemId) {
            $query->where('inventory_item_id', $searchDTO->inventoryItemId);
        }

        // Filter by quantity range
        if ($searchDTO->minQuantity !== null) {
            $query->where('quantity', '>=', $searchDTO->minQuantity);
        }

        if ($searchDTO->maxQuantity !== null) {
            $query->where('quantity', '<=', $searchDTO->maxQuantity);
        }

        // Filter low stock items
        if ($searchDTO->lowStockOnly) {
            $query->where('is_low_stock', true);
        }

        return $query->orderBy('updated_at', 'desc')
            ->paginate($searchDTO->perPage);
    }

    public function create(array $data): Stock
    {
        return Stock::create($data);
    }

    public function update(int $id, array $data): bool
    {
        return Stock::where('id', $id)->update($data);
    }

    public function delete(int $id): bool
    {
        return Stock::destroy($id) > 0;
    }

    public function findByWarehouseAndItem(int $warehouseId, int $itemId): ?Stock
    {
        return Stock::where('warehouse_id', $warehouseId)
            ->where('inventory_item_id', $itemId)
            ->first();
    }

    public function findOrCreateStock(int $warehouseId, int $itemId): Stock
    {
        return Stock::firstOrCreate([
            'warehouse_id' => $warehouseId,
            'inventory_item_id' => $itemId,
        ], ['quantity' => 0]);
    }

    public function updateQuantity(int $id, int $quantity): bool
    {
        return Stock::where('id', $id)->update(['quantity' => $quantity]);

    }

    public function getStockByWarehouse(int $warehouseId): Collection
    {
        return Stock::with(['inventoryItem'])
            ->where('warehouse_id', $warehouseId)
            ->get();
    }
}
