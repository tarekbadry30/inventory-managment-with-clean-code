<?php

namespace App\Contracts;

use App\Models\StockTransfer;
use App\Enums\TransferStatus;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface StockTransferRepositoryInterface
{
    public function findById(int $id): ?StockTransfer;

    public function findByIdWithRelations(int $id): ?StockTransfer;

    public function getAll(DTOInterface $filters): LengthAwarePaginator;

    public function create(array $data): StockTransfer;

    public function update(int $id, array $data): bool;

    public function updateStatus(int $id, TransferStatus $status): bool;

    public function getPendingTransfers(): Collection;

    public function getStatistics(): array;
}
