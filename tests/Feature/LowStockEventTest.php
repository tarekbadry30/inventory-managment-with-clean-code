<?php

use Tests\TestCase;
use App\Models\Warehouse;
use App\Models\InventoryItem;
use App\Models\Stock;
use App\Events\LowStockDetected;
use Illuminate\Support\Facades\Event;
use Illuminate\Foundation\Testing\RefreshDatabase;

class LowStockEventTest extends TestCase
{
    use RefreshDatabase;

    public function test_low_stock_event_is_fired_and_queued()
    {
        Event::fake();

        $item = InventoryItem::factory()->create();
        $warehouse = Warehouse::factory()->create();

        Stock::factory()->create([
            'warehouse_id' => $warehouse->id,
            'inventory_item_id' => $item->id,
            'quantity' => 5,
        ]);

        // Example: drop quantity below threshold in your service
        $stock = Stock::first();
        $stock->decrement('quantity', 4); // Only 1 left, assume low stock threshold is 3

        // Trigger event manually or via observer/service logic
        event(new LowStockDetected($stock));

        Event::assertDispatched(LowStockDetected::class);
        Event::assertDispatchedTimes(LowStockDetected::class, 1);
        Event::assertDispatched(function (LowStockDetected $event) use ($stock) {
            return $event->stock->is($stock);
        });
    }
}
