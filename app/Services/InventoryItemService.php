<?php

namespace App\Services;

use Illuminate\Support\Str;
use App\Models\InventoryItem;
use App\Contracts\DTOInterface;
use App\Contracts\InventoryItemRepositoryInterface;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class InventoryItemService
{
    public function __construct(
        private InventoryItemRepositoryInterface $inventoryItemRepository,
    ) {}

    public function search(DTOInterface $filters): LengthAwarePaginator
    {
        return $this->inventoryItemRepository->search($filters);
    }

    public function create(array $data): InventoryItem
    {

        if ($data['sku'] ?? null  != null && $this->inventoryItemRepository->findBySku($data['sku'])) {
            throw new \InvalidArgumentException('SKU already exists');
        } else if (!isset($data['sku']) || empty($data['sku'])) {
            $data['sku'] = $this->generateSKU($data['name'] ?? '', $data['price'] ?? 0);
        }

        return $this->inventoryItemRepository->create($data);
    }

    public function update(int $id, array $data): bool
    {
        $item = $this->inventoryItemRepository->findById($id);

        if (!$item) {
            throw new ModelNotFoundException('Inventory item not found');
        }

        if (isset($data['sku']) && $data['sku'] !== $item->sku) {
            $existingItem = $this->inventoryItemRepository->findBySku($data['sku']);
            if ($existingItem && $existingItem->id !== $id) {
                throw new \InvalidArgumentException('SKU already exists');
            }
        }

        return $this->inventoryItemRepository->update($id, $data);
    }

    public function destroy(int $id): bool
    {
        $item = $this->inventoryItemRepository->findById($id);

        if (!$item) {
            throw new ModelNotFoundException('Inventory item not found');
        }

        if ($item->stocks()->exists()) {
            throw new \InvalidArgumentException('Cannot delete item with existing stock');
        }
        $item->delete();
        return $this->inventoryItemRepository->delete($id);
    }

    public function getDetails(int $id): ?InventoryItem
    {
        return $this->inventoryItemRepository->findByIdWithRelations($id, ['stocks.warehouse']);
    }

    public function findByItemIdAndWarehouseId(int $itemId, int $warehouseId): ?InventoryItem
    {
        return $this->inventoryItemRepository->findByItemIdAndWarehouseId($itemId, $warehouseId);
    }

    public function generateSKU(string $name = '', $price = ''): string
    {
        $baseSKU = strtoupper(substr($name, 0, 3)) . '-' . intval($price * 100);
        $sku = $baseSKU . '-' . strtoupper(Str::random(5));
        while ($this->inventoryItemRepository->findBySku($sku)) {
            $sku = $baseSKU . '-' . strtoupper(Str::random(5));
        }
        return $sku;
    }
}
