<?php

namespace App\Services;

use App\Models\Stock;
use App\Events\LowStockDetected;
use App\DTOs\Stock\StockCreateDTO;
use App\DTOs\Stock\StockUpdateDTO;
use App\DTOs\Stock\StockSearchDTO;
use App\Contracts\StockRepositoryInterface;
use App\Services\CacheService;
use App\Repositories\InventoryItemRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class StockService
{
    public function __construct(
        private StockRepositoryInterface $stockRepository,
        private CacheService $cacheService
    ) {}

    public function search(StockSearchDTO $searchDTO): LengthAwarePaginator
    {
        return $this->stockRepository->search($searchDTO);
    }

    public function findById(int $id): ?Stock
    {
        return $this->stockRepository->findById($id, ['warehouse', 'inventoryItem']);
    }

    public function create(StockCreateDTO $dto): Stock
    {
        // Check if stock already exists for this warehouse and item
        $existingStock = $this->stockRepository->findByWarehouseAndItem(
            $dto->warehouseId,
            $dto->inventoryItemId
        );

        if ($existingStock) {
            throw new \InvalidArgumentException(
                'Stock already exists for this item in this warehouse. Use update instead.'
            );
        }
        $inventoryItem = (app(InventoryItemRepository::class))->findById($dto->inventoryItemId);
        $isLowStock = $dto->quantity <= $inventoryItem->low_stock_threshold;
        $data = $dto->toArray();
        $data['is_low_stock'] = $isLowStock;
        $stock = $this->stockRepository->create($data);

        // Clear related cache
        $this->cacheService->clearWarehouse($stock->warehouse_id);

        // Check for low stock and trigger event if necessary
        $this->checkAndTriggerLowStockEvent($stock);

        return $stock;
    }

    public function update(int $id, StockUpdateDTO $dto): Stock
    {
        $stock = $this->stockRepository->findById($id);

        if (!$stock) {
            throw new ModelNotFoundException('Stock not found');
        }

        $this->stockRepository->update($id, $dto->toArray());

        // Reload the stock to get updated data
        $updatedStock = $this->stockRepository->findById($id);

        // Clear related cache
        $this->cacheService->clearWarehouse($updatedStock->warehouse_id);
        // Check for low stock and trigger event if necessary
        $this->checkAndTriggerLowStockEvent($updatedStock);

        return $updatedStock;
    }

    public function destroy(int $id): bool
    {
        $stock = $this->stockRepository->findById($id);

        if (!$stock) {
            throw new ModelNotFoundException('Stock not found');
        }

        $warehouseId = $stock->warehouse_id;
        $deleted = $this->stockRepository->delete($id);

        if ($deleted) {
            // Clear related cache
            $this->cacheService->clearWarehouse($warehouseId);
        } else {
            throw new \RuntimeException('Failed to delete stock');
        }

        return $deleted;
    }

    public function updateQuantity(int $id, int $quantity): Stock
    {
        if ($quantity < 0) {
            throw new \InvalidArgumentException('Quantity cannot be negative');
        }

        $stock = $this->stockRepository->findById($id, ['inventoryItem']);

        if (!$stock) {
            throw new ModelNotFoundException('Stock not found');
        }
        $inventoryItem = $stock->inventoryItem;
        $isLowStock = $quantity <= $inventoryItem->low_stock_threshold;
        $data = ['quantity' => $quantity, 'is_low_stock' => $isLowStock];

        $this->stockRepository->update($id, $data);

        // Reload the stock to get updated data
        $updatedStock = $this->stockRepository->findById($id, ['warehouse', 'inventoryItem']);

        // Clear related cache
        $this->cacheService->clearWarehouse($updatedStock->warehouse_id);

        // Check for low stock and trigger event if necessary
        $this->checkAndTriggerLowStockEvent($updatedStock);

        return $updatedStock;
    }


    private function checkAndTriggerLowStockEvent(Stock $stock): void
    {
        if ($stock->is_low_stock) {
            event(new LowStockDetected($stock));
        }
    }
}
