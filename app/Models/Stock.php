<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Stock extends Model
{
    use HasFactory;

    protected $fillable = [
        'warehouse_id',
        'inventory_item_id',
        'quantity',
        'is_low_stock',
    ];

    /**
     * Get the warehouse that owns the stock.
     */
    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class);
    }

    /**
     * Get the inventory item that owns the stock.
     */
    public function inventoryItem(): BelongsTo
    {
        return $this->belongsTo(InventoryItem::class);
    }

    /**
     * Reduce the stock quantity.
     */
    public function reduceQuantity(int $amount): void
    {
        $this->quantity = max(0, $this->quantity - $amount);
        $this->is_low_stock = $this->quantity < $this->inventoryItem->low_stock_threshold;
        $this->save();
    }
    /**
     * Increase the stock quantity.
     */
    public function increaseQuantity(int $amount): void
    {
        $this->quantity += $amount;
        $this->is_low_stock = $this->quantity < $this->inventoryItem->low_stock_threshold;
        $this->save();
    }

}
