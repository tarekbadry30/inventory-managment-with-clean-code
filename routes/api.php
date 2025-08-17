<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\InventoryItemController;
use App\Http\Controllers\API\StockController;
use App\Http\Controllers\API\WarehouseController;
use App\Http\Controllers\API\StockTransferController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Authentication routes
Route::prefix('auth')->group(function () {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('logout', [AuthController::class, 'logout']);
        Route::get('user', [AuthController::class, 'user']);
    });
});

// Protected routes
Route::middleware('auth:sanctum')->group(function () {


    // Warehouse routes
    Route::apiResource('warehouses', WarehouseController::class)->except(['show']);
    Route::get('warehouses/{warehouse}/inventory', [WarehouseController::class, 'inventory']);

    // Inventory routes
    Route::apiResource('inventory-items', InventoryItemController::class);

    // Stock routes
    Route::apiResource('stocks', StockController::class);
    Route::patch('stocks/{id}/quantity', [StockController::class, 'updateQuantity']);

    // Stock transfer routes
    Route::apiResource('stock-transfers', StockTransferController::class)->except(['destroy', 'update']);
    Route::post('stock-transfers/{stockTransfer}/cancel', [StockTransferController::class, 'cancel']);
    Route::post('stock-transfers/{stockTransfer}/accept', [StockTransferController::class, 'accept']);
    Route::get('stock-transfers-statistics', [StockTransferController::class, 'statistics']);
});
