<?php

namespace App\Services;

use Carbon\Carbon;
use App\Enums\TransferStatus;
use App\Models\StockTransfer;
use App\Contracts\DTOInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Jobs\CheckLowStockOfItemJob;
use App\Contracts\StockRepositoryInterface;
use App\DTOs\StockTransfer\StockTransferStoreDTO;
use App\Contracts\StockTransferRepositoryInterface;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class StockTransferService
{
    public function __construct(
        private StockTransferRepositoryInterface $transferRepository,
        private StockRepositoryInterface $stockRepository,
        private CacheService $cacheService
    ) {}

    public function getAll(DTOInterface $filters): LengthAwarePaginator
    {
        return $this->transferRepository->getAll($filters);
    }
    public function createTransfer(StockTransferStoreDTO $dto): StockTransfer
    {
        return DB::transaction(function () use ($dto) {
            // Validate the transfer
            $this->validateTransfer($dto);

            // Create the transfer
            $transfer = $this->transferRepository->create($dto->toArray());

            // Execute the transfer if status is COMPLETED
            if ($transfer->status === TransferStatus::COMPLETED->value) {
                $this->executeTransfer($transfer->id, false);
                $transfer->fresh();
            }

            return $transfer;
        });
    }

    public function executeTransfer(int $transferId, bool $onlyPending = true): bool
    {
        return DB::transaction(function () use ($transferId, $onlyPending) {
            $transfer = $this->transferRepository->findById($transferId);
            if (!$transfer) {
                throw new ModelNotFoundException('Stock Transfer not found');
            }
            if ($onlyPending && $transfer->status->value !== TransferStatus::PENDING->value) {
                throw new \InvalidArgumentException('Stock Transfer status is not pending, it is ' . $transfer->status?->value);
            }

            // Get or create stock records
            $fromStock = $this->stockRepository->findOrCreateStock(
                $transfer->from_warehouse_id,
                $transfer->inventory_item_id
            );

            $toStock = $this->stockRepository->findOrCreateStock(
                $transfer->to_warehouse_id,
                $transfer->inventory_item_id
            );

            // Check if enough stock is available
            if ($fromStock->quantity < $transfer->quantity) {
                Log::warning('Insufficient stock for transfer', [
                    'transfer_id' => $transferId,
                    'required' => $transfer->quantity,
                    'available' => $fromStock->quantity
                ]);
                throw new \InvalidArgumentException('Insufficient stock for transfer, required: ' . $transfer->quantity . ', available: ' . $fromStock->quantity);
            }

            // Perform the transfer
            $fromStock->reduceQuantity($transfer->quantity);
            $toStock->increaseQuantity($transfer->quantity);

            // Update transfer status
            $this->transferRepository->update($transferId, [
                'status' => TransferStatus::COMPLETED->value,
                'transferred_at' => Carbon::now(),
            ]);

            // Clear cache for both warehouses
            $this->cacheService->clearWarehouse($transfer->from_warehouse_id);
            $this->cacheService->clearWarehouse($transfer->to_warehouse_id);

            Log::info('Stock transfer completed successfully', [
                'transfer_id' => $transferId,
                'from_warehouse' => $transfer->from_warehouse_id,
                'to_warehouse' => $transfer->to_warehouse_id,
                'item_id' => $transfer->inventory_item_id,
                'quantity' => $transfer->quantity
            ]);
            CheckLowStockOfItemJob::dispatch($transfer->inventory_item_id, $transfer->to_warehouse_id);
            CheckLowStockOfItemJob::dispatch($transfer->inventory_item_id, $transfer->from_warehouse_id);

            return true;
        });
    }

    public function cancelTransfer(int $transferId): bool
    {
        $transfer = $this->transferRepository->findById($transferId);

        if (!$transfer || $transfer->status !== TransferStatus::PENDING->value) {
            return false;
        }

        return $this->transferRepository->updateStatus($transferId, TransferStatus::CANCELLED);
    }

    public function getTransferDetails(int $transferId): ?StockTransfer
    {
        return $this->transferRepository->findByIdWithRelations($transferId, [
            'fromWarehouse',
            'toWarehouse',
            'inventoryItem',
            'creator'
        ]);
    }

    private function validateTransfer(StockTransferStoreDTO $dto): void
    {

        // Check if warehouses are different
        if ($dto->fromWarehouseId === $dto->toWarehouseId) {
            throw new \InvalidArgumentException('Source and destination warehouses must be different');
        }

        //check if transfer from exists inventory or new item
        if ($dto->fromWarehouseId !== null) {

            // Check if enough stock is available
            $stock = $this->stockRepository->findByWarehouseAndItem(
                $dto->fromWarehouseId,
                $dto->inventoryItemId
            );

            if (!$stock || $stock->quantity < $dto->quantity) {
                $available = $stock ? $stock->quantity : 0;
                throw new \InvalidArgumentException("Insufficient stock available. Required: {$dto->quantity}, Available: {$available}");
            }
        }
    }
}
