<?php

namespace App\Repositories;

use App\Contracts\DTOInterface;
use App\Contracts\InventoryItemRepositoryInterface;
use App\Models\InventoryItem;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class InventoryItemRepository implements InventoryItemRepositoryInterface
{
    public function findById(int $id): ?InventoryItem
    {
        return InventoryItem::find($id);
    }
    public function findByItemIdAndWarehouseId(int $itemId, int $warehouseId): ?InventoryItem
    {
        return InventoryItem::
            with(['stocks' => function ($query) use ($warehouseId) {
                $query->where('warehouse_id', $warehouseId);
            }])
            ->where('id', $itemId)
            ->whereHas('stocks', function ($query) use ($warehouseId) {
                $query->where('warehouse_id', $warehouseId);
            })->first();
    }
    public function findByIdWithRelations(int $id, array $relations = []): ?InventoryItem
    {
        return InventoryItem::with($relations)->find($id);
    }

    public function findBySku($sku): ?InventoryItem
    {
        return InventoryItem::where('sku', $sku)->first();
    }

    public function search(DTOInterface $filters): LengthAwarePaginator
    {
        return InventoryItem::with(['stocks.warehouse'])->when($filters->search, function ($query) use ($filters) {
            $query->search($filters->search);
        })->when($filters->minPrice !== null || $filters->maxPrice !== null, function ($query) use ($filters) {
            $query->priceRange($filters->minPrice, $filters->maxPrice);
        })->when($filters->warehouseId, function ($query) use ($filters) {
            $query->whereHas('stocks', function ($q) use ($filters) {
                $q->where('warehouse_id', $filters->warehouseId)
                    ->where('quantity', '>', 0);
            });
        })->paginate($filters->perPage);
    }

    public function create(array $data): InventoryItem
    {
        return InventoryItem::create($data);
    }

    public function update(int $id, array $data): bool
    {
        return InventoryItem::where('id', $id)->update($data);
    }

    public function delete(int $id): bool
    {
        return InventoryItem::destroy($id) > 0;
    }

    public function getAllWithStocks(): Collection
    {
        return InventoryItem::with('stocks.warehouse')->get();
    }
}
