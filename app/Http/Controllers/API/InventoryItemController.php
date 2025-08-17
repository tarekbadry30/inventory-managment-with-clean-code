<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use App\Services\InventoryItemService;
use App\Http\Controllers\Controller;
use App\DTOs\InventoryItem\InventoryItemsSearchDTO;
use App\Http\Requests\InventoryItem\StoreInventoryItemRequest;
use App\Http\Requests\InventoryItem\UpdateInventoryItemRequest;
use App\Http\Requests\InventoryItem\InventoryItemIndexRequest;
use App\Http\Resources\InventoryItem\InventoryItemBasicResource;
use App\Http\Resources\InventoryItem\InventoryItemResource;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class InventoryItemController extends Controller
{
    public function __construct(
        private InventoryItemService $inventoryItemService
    ) {}


    /**
     * Get paginated inventory items with search and filtering.
     */
    public function index(InventoryItemIndexRequest $request): JsonResponse
    {
        $filters = InventoryItemsSearchDTO::fromArray($request->validated());
        $items = $this->inventoryItemService->search($filters);
        return sendSuccessResponse(InventoryItemBasicResource::withPagination($items));
    }

    /**
     * Create a new inventory item.
     */
    public function store(StoreInventoryItemRequest $request): JsonResponse
    {
        $item = $this->inventoryItemService->create($request->validated());
        return sendSuccessResponse(InventoryItemResource::make($item), 'Inventory item created successfully', Response::HTTP_CREATED);
    }

    /**
     * Show a specific inventory item.
     */
    public function show($id): JsonResponse
    {
        $item = $this->inventoryItemService->getDetails($id);
        if (!$item) {
            return sendErrorResponse('Inventory item not found', [], Response::HTTP_NOT_FOUND);
        }
        return sendSuccessResponse(InventoryItemResource::make($item));
    }

    /**
     * Update an inventory item.
     */
    public function update(UpdateInventoryItemRequest $request, int $id): JsonResponse
    {
        $this->inventoryItemService->update($id, $request->validated());
        return sendSuccessResponse(InventoryItemResource::make($this->inventoryItemService->getDetails($id)), 'Inventory item updated successfully');
    }

    /**
     * Delete an inventory item.
     */
    public function destroy(int $id): JsonResponse
    {
        $this->inventoryItemService->destroy($id);
        return sendSuccessResponse(null, 'Inventory item deleted successfully');
    }
}
