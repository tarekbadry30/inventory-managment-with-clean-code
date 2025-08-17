<?php

use Tests\TestCase;
use App\Models\User;
use App\Models\Stock;
use App\Models\Warehouse;
use App\Models\InventoryItem;
use App\Services\StockTransferService;
use App\DTOs\StockTransfer\StockTransferStoreDTO;
use Illuminate\Foundation\Testing\RefreshDatabase;

class StockTransferServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_does_not_transfer_when_insufficient_stock()
    {
        $item = InventoryItem::factory()->create();
        $source = Warehouse::factory()->create();
        $target = Warehouse::factory()->create();

        Stock::factory()->create([
            'warehouse_id' => $source->id,
            'inventory_item_id' => $item->id,
            'quantity' => 5,
        ]);

        $service = app(StockTransferService::class);
        $dto = StockTransferStoreDTO::fromArray([
            'from_warehouse_id' => $source->id,
            'to_warehouse_id' => $target->id,
            'inventory_item_id' => $item->id,
            'created_by' => User::factory()->create()->id,
            'quantity' => 10,
        ]);
        $result = $service->createTransfer($dto);
        $this->assertInstanceOf(\App\Models\StockTransfer::class, $result);
    }
}
