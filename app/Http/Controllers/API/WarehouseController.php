<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\JsonResponse;
use App\Services\WarehouseService;
use App\Http\Controllers\Controller;
use App\Http\Resources\Warehouse\WarehouseResource;
use App\DTOs\Warehouse\WarehouseFilterDTO;
use Symfony\Component\HttpFoundation\Response;
use App\DTOs\Warehouse\WarehouseInventoryFilterDTO;
use App\Http\Requests\Warehouse\WarehouseIndexRequest;
use App\Http\Requests\Warehouse\WarehouseStoreRequest;
use App\Http\Requests\Warehouse\WarehouseUpdateRequest;
use App\Http\Resources\Warehouse\WarehouseBasicResource;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Http\Requests\Warehouse\WarehouseInventoryRequest;


class WarehouseController extends Controller
{
    public function __construct(
        private WarehouseService $warehouseService,
    ) {}


    /**
     * Get all warehouses.
     */
    public function index(WarehouseIndexRequest $request): JsonResponse
    {
        $filters = WarehouseFilterDTO::fromArray($request->validated());

        $warehouses = $this->warehouseService->search($filters);

        return sendSuccessResponse(WarehouseBasicResource::withPagination($warehouses));
    }

    /**
     * Create a new warehouse.
     */
    public function store(WarehouseStoreRequest $request): JsonResponse
    {
        $warehouse = $this->warehouseService->create($request->validated());
        return sendSuccessResponse(WarehouseResource::make($warehouse), 'Warehouse created successfully', Response::HTTP_CREATED);
    }

    /**
     * Get inventory for a specific warehouse with caching.
     */
    public function inventory(WarehouseInventoryRequest $request, int $id): JsonResponse
    {

        $filters = WarehouseInventoryFilterDTO::fromArray($request->validated());
        $warehouse = $this->warehouseService->findByIdWithInventoryItems($id, $filters);

        return sendSuccessResponse(WarehouseResource::make($warehouse), "get warehouse with it's inventory");
    }

    /**
     * Update a warehouse.
     */
    public function update(WarehouseUpdateRequest $request, $id): JsonResponse
    {
        $updated = $this->warehouseService->update($id, $request->validated());
        if ($updated) {
            return sendSuccessResponse(WarehouseResource::make($this->warehouseService->findById($id)), 'Warehouse updated successfully');
        } else {
            return sendErrorResponse('Warehouse not found', [], Response::HTTP_NOT_FOUND);
        }
    }

    /**
     * Delete a warehouse.
     */
    public function destroy($id): JsonResponse
    {
        $deleted = $this->warehouseService->destroy($id);
        if ($deleted) {
            return sendSuccessResponse(null, 'Warehouse deleted successfully');
        } else {
            return sendErrorResponse('Warehouse not deleted', [], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }
}
