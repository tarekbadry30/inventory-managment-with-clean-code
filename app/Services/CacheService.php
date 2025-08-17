<?php

namespace App\Services;

use App\Models\Warehouse;
use Illuminate\Support\Facades\Cache;

class CacheService
{
    private const DEFAULT_TTL = 300; // 5 minutes
    const CACHE_WAREHOUSE_KEY = 'warehouse_inventory_items';

    public function cacheWarehouseWithItems(int $warehouseId, Warehouse $warehouse): bool
    {
        $key = self::CACHE_WAREHOUSE_KEY . '_' . $warehouseId;
        return Cache::put($key, $warehouse, self::DEFAULT_TTL);
    }
    public function getWarehouseWithItems(int $warehouseId): mixed
    {
        $key = self::CACHE_WAREHOUSE_KEY . '_' . $warehouseId;
        return Cache::get($key);
    }
    public function clearWarehouse(int $warehouseId): bool
    {
        $key = self::CACHE_WAREHOUSE_KEY . '_' . $warehouseId;
        return Cache::forget($key);
    }

    public function generateCacheKey(int $modelId, string $modelName): string
    {
        return $modelName . '_' . $modelId;
    }

    public function clearCacheByKey(string $cacheKey): bool
    {
        return Cache::forget($cacheKey);
    }
}
