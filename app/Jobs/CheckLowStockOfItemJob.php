<?php

namespace App\Jobs;

use App\Services\InventoryItemService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class CheckLowStockOfItemJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $inventoryItemId;
    public $warehouseId;
    /**
     * Create a new job instance.
     */
    public function __construct(int $inventoryItemId, int $warehouseId)
    {
        $this->inventoryItemId = $inventoryItemId;
        $this->warehouseId = $warehouseId;
    }


    /**
     * Execute the job.
     */
    public function handle(InventoryItemService $inventoryItemService): void
    {
        $item=$inventoryItemService->findByItemIdAndWarehouseId($this->inventoryItemId, $this->warehouseId);
        if(!$item) {
            Log::warning('Inventory item not found for ID: ' . $this->inventoryItemId . ' in warehouse ID: ' . $this->warehouseId);
            return;
        }
        $stock=$item?->stocks?->where('warehouse_id', $this->warehouseId)->first();
        if(!$stock) {
            Log::warning('Stock not found for item ID: ' . $this->inventoryItemId . ' in warehouse ID: ' . $this->warehouseId);
            return;
        }
        if ($item && $stock && $stock->quantity <= $item->low_stock_threshold) {
            event(new \App\Events\LowStockDetected($stock));
        } else {
            // Handle case where item or stock does not exist
        }
    }
}
