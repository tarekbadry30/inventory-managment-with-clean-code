<?php

namespace App\Http\Controllers\API;

use App\Services\StockService;
use Illuminate\Http\JsonResponse;
use App\DTOs\Stock\StockSearchDTO;
use App\DTOs\Stock\StockCreateDTO;
use App\DTOs\Stock\StockUpdateDTO;
use App\Http\Controllers\Controller;
use App\Http\Resources\Stock\StockResource;
use App\Http\Resources\Stock\StockBasicResource;
use App\Http\Requests\Stock\StockIndexRequest;
use App\Http\Requests\Stock\StockStoreRequest;
use App\Http\Requests\Stock\StockUpdateRequest;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpFoundation\Response;

class StockController extends Controller
{
    public function __construct(
        private StockService $stockService
    ) {}

    /**
     * Get paginated stock items with search and filtering.
     */
    public function index(StockIndexRequest $request): JsonResponse
    {
        $filters = StockSearchDTO::fromArray($request->validated());
        $stocks = $this->stockService->search($filters);

        return sendSuccessResponse(
            StockBasicResource::withPagination($stocks),
            'Stocks retrieved successfully'
        );
    }

    /**
     * Create a new stock record.
     */
    public function store(StockStoreRequest $request): JsonResponse
    {
        $dto = StockCreateDTO::fromArray($request->validated());
        $stock = $this->stockService->create($dto);

        return sendSuccessResponse(
            new StockResource($stock),
            'Stock created successfully',
            Response::HTTP_CREATED
        );
    }

    /**
     * Show a specific stock record.
     */
    public function show($id): JsonResponse
    {
        $stock = $this->stockService->findById($id);

        if (!$stock) {
            return sendErrorResponse('Stock not found', [], 404);
        }

        return sendSuccessResponse(
            new StockResource($stock),
            'Stock retrieved successfully'
        );
    }

    /**
     * Update a stock record.
     */
    public function update(StockUpdateRequest $request, $id): JsonResponse
    {
        $dto = StockUpdateDTO::fromArray($request->validated());
        $stock = $this->stockService->update($id, $dto);

        return sendSuccessResponse(
            new StockResource($stock),
            'Stock updated successfully'
        );
    }

    /**
     * Delete a stock record.
     */
    public function destroy($id): JsonResponse
    {
        $deleted = $this->stockService->destroy($id);

        if ($deleted) {
            return sendSuccessResponse(null, 'Stock deleted successfully');
        } else {
            return sendErrorResponse('Stock not found', [], 404);
        }
    }


    /**
     * Update stock quantity.
     */
    public function updateQuantity(StockUpdateRequest $request, $id): JsonResponse
    {
        $quantity = $request->validated()['quantity'];
        $stock = $this->stockService->updateQuantity($id, $quantity);

        return sendSuccessResponse(
            new StockResource($stock),
            'Stock quantity updated successfully'
        );
    }
}
