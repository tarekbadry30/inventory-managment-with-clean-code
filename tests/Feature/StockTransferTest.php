<?php

use Tests\TestCase;
use App\Models\User;
use App\Models\Stock;
use App\Models\Warehouse;
use App\Enums\TransferStatus;
use App\Models\InventoryItem;
use Illuminate\Http\Response;
use Illuminate\Foundation\Testing\RefreshDatabase;

class StockTransferTest extends TestCase
{
    use RefreshDatabase;

    public function test_successful_stock_transfer()
    {
        $item = InventoryItem::factory()->create();
        $source = Warehouse::factory()->create();
        $target = Warehouse::factory()->create();

        Stock::factory()->create([
            'warehouse_id' => $source->id,
            'inventory_item_id' => $item->id,
            'quantity' => 20,
        ]);

        $response = $this->actingAs(User::factory()->create())->postJson('/api/stock-transfers', [
            'inventory_item_id' => $item->id,
            'from_warehouse_id' => $source->id,
            'to_warehouse_id' => $target->id,
            'status' => TransferStatus::COMPLETED->value,
            'quantity' => 10,
        ]);

        $response->assertStatus(Response::HTTP_CREATED);
        $this->assertDatabaseHas('stocks', [
            'warehouse_id' => $target->id,
            'inventory_item_id' => $item->id,
            'quantity' => 10,
        ]);

        $this->assertDatabaseHas('stocks', [
            'warehouse_id' => $source->id,
            'inventory_item_id' => $item->id,
            'quantity' => 10, // 20 - 10 transferred
        ]);
        $this->assertDatabaseHas('stock_transfers', [
            'inventory_item_id' => $item->id,
            'from_warehouse_id' => $source->id,
            'to_warehouse_id' => $target->id,
            'quantity' => 10,
            'status' => TransferStatus::COMPLETED->value,
        ]);
    }

    public function test_pending_transfer_does_not_change_stock()
    {
        $item = InventoryItem::factory()->create();
        $source = Warehouse::factory()->create();
        $target = Warehouse::factory()->create();

        Stock::factory()->create([
            'warehouse_id' => $source->id,
            'inventory_item_id' => $item->id,
            'quantity' => 20,
        ]);

        $response = $this->actingAs(User::factory()->create())->postJson('/api/stock-transfers', [
            'inventory_item_id' => $item->id,
            'from_warehouse_id' => $source->id,
            'to_warehouse_id' => $target->id,
            'status' => TransferStatus::PENDING->value,
            'quantity' => 10,
        ]);

        $response->assertStatus(Response::HTTP_CREATED);

        $this->assertDatabaseHas('stocks', [
            'warehouse_id' => $source->id,
            'inventory_item_id' => $item->id,
            'quantity' => 20,
        ]);

        $this->assertDatabaseMissing('stocks', [
            'warehouse_id' => $target->id,
            'inventory_item_id' => $item->id,
        ]);
    }
}
