<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Warehouse extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'location',
        'description',
    ];

    /**
     * Get the stocks for the warehouse.
     */
    public function stocks(): HasMany
    {
        return $this->hasMany(Stock::class);
    }

    /**
     * Get the outgoing stock transfers from this warehouse.
     */
    public function outgoingTransfers(): HasMany
    {
        return $this->hasMany(StockTransfer::class, 'from_warehouse_id');
    }

    /**
     * Get the incoming stock transfers to this warehouse.
     */
    public function incomingTransfers(): HasMany
    {
        return $this->hasMany(StockTransfer::class, 'to_warehouse_id');
    }

    /**
     * Get the inventory items in this warehouse through stocks.
     * Many-to-many relationship through the stocks pivot table.
     */
    public function inventoryItems(): BelongsToMany
    {
        return $this->belongsToMany(InventoryItem::class, 'stocks', 'warehouse_id', 'inventory_item_id')
            ->withPivot(['quantity', 'is_low_stock', 'created_at', 'updated_at'])
            ->withTimestamps();
    }
}
