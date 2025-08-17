<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Contracts\InventoryItemRepositoryInterface;
use App\Contracts\WarehouseRepositoryInterface;
use App\Contracts\StockRepositoryInterface;
use App\Contracts\StockTransferRepositoryInterface;
use App\Repositories\InventoryItemRepository;
use App\Repositories\WarehouseRepository;
use App\Repositories\StockRepository;
use App\Repositories\StockTransferRepository;
use App\Services\StockTransferService;
use App\Services\CacheService;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // Repository bindings
        $this->app->bind(InventoryItemRepositoryInterface::class, InventoryItemRepository::class);
        $this->app->bind(WarehouseRepositoryInterface::class, WarehouseRepository::class);
        $this->app->bind(StockRepositoryInterface::class, StockRepository::class);
        $this->app->bind(StockTransferRepositoryInterface::class, StockTransferRepository::class);
        $this->app->bind(\App\Contracts\UserRepositoryInterface::class, \App\Repositories\UserRepository::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
