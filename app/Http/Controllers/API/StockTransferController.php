<?php

namespace App\Http\Controllers\API;

use App\Models\StockTransfer;
use App\DTOs\StockTransfer\StockTransferStoreDTO;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Services\StockTransferService;
use App\Http\Requests\StockTransfer\StoreStockTransferRequest;
use App\Http\Resources\Stock\StockTransferResource;
use App\DTOs\StockTransfer\StockTransferFilterDTO;
use App\Http\Requests\StockTransfer\StockTransferIndexRequest;
use App\Contracts\StockTransferRepositoryInterface;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Response;

class StockTransferController extends Controller
{
    public function __construct(
        private StockTransferService $stockTransferService,
        private StockTransferRepositoryInterface $stockTransferRepository
    ) {}

    /**
     * Get all stock transfers with pagination.
     */
    public function index(StockTransferIndexRequest $request): JsonResponse
    {

        $filters = StockTransferFilterDTO::fromArray($request->validated());

        $transfers = $this->stockTransferService->getAll($filters);

        return sendSuccessResponse(StockTransferResource::withPagination($transfers), 'get Stock transfers');
    }

    /**
     * Create a new stock transfer.
     */
    public function store(StoreStockTransferRequest $request): JsonResponse
    {
        $data = $request->validated();
        $data['created_by'] = auth()->id();

        $transfer = $this->stockTransferService->createTransfer(StockTransferStoreDTO::fromArray($data));

        return sendSuccessResponse(new StockTransferResource($transfer->load(['fromWarehouse', 'toWarehouse', 'inventoryItem', 'creator'])), 'Stock transfer completed successfully', Response::HTTP_CREATED);
    }

    /**
     * Show a specific stock transfer.
     */
    public function show(StockTransfer $stockTransfer): JsonResponse
    {
        return sendSuccessResponse($this->stockTransferService->getTransferDetails($stockTransfer->id), 'Stock transfer details retrieved successfully');
    }

    /**
     * Cancel a stock transfer.
     */
    public function cancel(int $id): JsonResponse
    {
        $success = $this->stockTransferService->cancelTransfer($id);

        if (!$success) {
            return sendErrorResponse('Only pending transfers can be cancelled', [], Response::HTTP_BAD_REQUEST);
        }

        return sendSuccessResponse(null, 'Stock transfer cancelled successfully');
    }
    public function accept(int $id): JsonResponse
    {
        $executed = $this->stockTransferService->executeTransfer($id);
        if (!$executed) {
            return sendErrorResponse('Only pending transfers can be accepted', [], Response::HTTP_BAD_REQUEST);
        }
        return sendSuccessResponse(null, 'Stock transfer accepted successfully');
    }

    /**
     * Get transfer statistics.
     */
    public function statistics(): JsonResponse
    {
        return sendSuccessResponse($this->stockTransferRepository->getStatistics(), 'Stock transfer statistics retrieved successfully');
    }
}
