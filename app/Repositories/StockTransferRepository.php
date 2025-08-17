<?php

namespace App\Repositories;

use App\Enums\TransferStatus;
use App\Models\StockTransfer;
use App\Contracts\DTOInterface;
use Illuminate\Support\Facades\DB;
use App\Contracts\StockRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use App\Contracts\StockTransferRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class StockTransferRepository implements StockTransferRepositoryInterface
{
    private $stockRepository;
    public function __construct(StockRepositoryInterface $stockRepository)
    {
        $this->stockRepository = $stockRepository;
    }

    public function findById(int $id): ?StockTransfer
    {
        return StockTransfer::find($id);
    }

    public function findByIdWithRelations(int $id): ?StockTransfer
    {
        return StockTransfer::with([
            'fromWarehouse',
            'toWarehouse',
            'inventoryItem',
            'creator'
        ])->findOrFail($id);
    }

    public function getAll(DTOInterface $filters): LengthAwarePaginator
    {
        return StockTransfer::with([
            'fromWarehouse',
            'toWarehouse',
            'inventoryItem',
            'creator'
        ])->when($filters->status, function ($query) use ($filters) {
            $query->where('status', $filters->status);
        })->when($filters->from_warehouse_id, function ($query) use ($filters) {
            $query->where('from_warehouse_id', $filters->from_warehouse_id);
        })->when($filters->to_warehouse_id, function ($query) use ($filters) {
            $query->where('to_warehouse_id', $filters->to_warehouse_id);
        })->when($filters->inventory_item_id, function ($query) use ($filters) {
            $query->where('inventory_item_id', $filters->inventory_item_id);
        })->orderBy('created_at', 'desc')->paginate($filters->perPage);
    }

    public function create(array $data): StockTransfer
    {
        return StockTransfer::create($data);
    }

    public function update(int $id, array $data): bool
    {
        return StockTransfer::where('id', $id)->update($data);
    }

    public function updateStatus(int $id, TransferStatus $status): bool
    {
        return StockTransfer::where('id', $id)->update(['status' => $status->value]);
    }

    public function getPendingTransfers(): Collection
    {
        return StockTransfer::where('status', TransferStatus::PENDING->value)
            ->with(['fromWarehouse', 'toWarehouse', 'inventoryItem'])
            ->get();
    }

    public function getStatistics(): array
    {
        return [
            'total_transfers' => StockTransfer::count(),
            'pending_transfers' => StockTransfer::where('status', TransferStatus::PENDING->value)->count(),
            'completed_transfers' => StockTransfer::where('status', TransferStatus::COMPLETED->value)->count(),
            'cancelled_transfers' => StockTransfer::where('status', TransferStatus::CANCELLED->value)->count(),
            'total_items_transferred' => StockTransfer::where('status', TransferStatus::COMPLETED->value)->sum('quantity'),
        ];
    }
}
