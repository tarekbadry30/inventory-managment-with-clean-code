# Inventory Management System - Architecture

## Overview

This Laravel application implementing a clean architecture with Repository pattern, Service layer, DTOs, and proper separation of concerns.

## Architecture Overview

```
┌─────────────────────────────────────────────────────────────┐
│                        API Layer                            │
├─────────────────────────────────────────────────────────────┤
│ Controllers (API\*)                                         │
│ - Handle HTTP requests/responses                            │
│ - Use Resources for consistent API output                   │
│ - Delegate business logic to Services                       │
├─────────────────────────────────────────────────────────────┤
│                      Service Layer                          │
├─────────────────────────────────────────────────────────────┤
│ Services                                                    │
│ - Business logic implementation                             │
│ - Transaction management                                    │
│ - Event dispatching                                         │
│ - Cache management                                          │
├─────────────────────────────────────────────────────────────┤
│                    Repository Layer                         │
├─────────────────────────────────────────────────────────────┤
│ Repositories                                                │
│ - Data access abstraction                                   │
│ - Query optimization                                        │
│ - Database interactions                                     │
├─────────────────────────────────────────────────────────────┤
│                      Data Layer                             │
├─────────────────────────────────────────────────────────────┤
│ Models, DTOs, Enums                                         │
│ - Data representation                                       │
│ - Type safety                                               │
│ - Data validation                                           │
└─────────────────────────────────────────────────────────────┘
```

## Directory Structure

```
app/
├── Console/                     # Artisan commands
├── Contracts/                   # Interfaces for dependency injection
│   ├── DTOInterface.php
│   ├── InventoryItemRepositoryInterface.php
│   ├── StockRepositoryInterface.php
│   ├── StockTransferRepositoryInterface.php
│   └── WarehouseRepositoryInterface.php
├── DTOs/                        # Data Transfer Objects (organized by domain)
│   ├── InventoryItem/
│   │   └── InventoryItemsSearchDTO.php
│   ├── Stock/
│   │   ├── StockCreateDTO.php
│   │   ├── StockSearchDTO.php
│   │   └── StockUpdateDTO.php
│   ├── StockTransfer/
│   │   ├── StockTransferFilterDTO.php
│   │   └── StockTransferStoreDTO.php
│   └── WareHouse/
│       ├── WarehouseInventoryDTO.php
│       └── WarehouseSearchDTO.php
├── Enums/                       # Type-safe enumerations
│   └── TransferStatus.php       # (PENDING, COMPLETED, CANCELLED)
├── Events/                      # Domain events
│   └── LowStockDetected.php
├── Exceptions/                  # Custom exceptions
├── Http/
│   ├── Controllers/API/         # API Controllers (thin layer)
│   │   ├── AuthController.php
│   │   ├── InventoryItemController.php
│   │   ├── StockController.php
│   │   ├── StockTransferController.php
│   │   └── WarehouseController.php
│   ├── Requests/                # Form request validation (organized by domain)
│   │   ├── InventoryItem/
│   │   │   ├── InventoryItemIndexRequest.php
│   │   │   ├── StoreInventoryItemRequest.php
│   │   │   └── UpdateInventoryItemRequest.php
│   │   ├── Stock/
│   │   │   ├── StockIndexRequest.php
│   │   │   ├── StockStoreRequest.php
│   │   │   └── StockUpdateRequest.php
│   │   ├── StockTransfer/
│   │   │   ├── StockTransferIndexRequest.php
│   │   │   └── StoreStockTransferRequest.php
│   │   └── Warehouse/
│   │       ├── WarehouseIndexRequest.php
│   │       ├── WarehouseInventoryRequest.php
│   │       ├── WarehouseStoreRequest.php
│   │       └── WarehouseUpdateRequest.php
│   └── Resources/               # API response formatting (organized by domain)
│       ├── InventoryItem/
│       │   ├── InventoryItemBasicResource.php
│       │   └── InventoryItemResource.php
│       ├── Stock/
│       │   ├── StockBasicResource.php
│       │   ├── StockResource.php
│       │   └── StockTransferResource.php
│       ├── User/
│       │   └── UserResource.php
│       └── Warehouse/
│           ├── WarehouseBasicResource.php
│           └── WarehouseResource.php
├── Jobs/                        # Queueable jobs
├── Listeners/                   # Event listeners
│   └── SendLowStockNotification.php
├── Models/                      # Eloquent models with relationships
│   ├── InventoryItem.php
│   ├── Stock.php
│   ├── StockTransfer.php
│   ├── User.php
│   └── Warehouse.php
├── Providers/                   # Service providers
│   └── RepositoryServiceProvider.php  # Dependency injection bindings
├── Repositories/                # Data access layer with contracts
│   ├── InventoryItemRepository.php
│   ├── StockRepository.php
│   ├── StockTransferRepository.php
│   └── WarehouseRepository.php
├── Services/                    # Business logic layer
│   ├── CacheService.php
│   ├── InventoryItemService.php
│   ├── StockService.php
│   ├── StockTransferService.php
│   └── WarehouseService.php
└── Traits/                      # Reusable traits

database/
├── factories/                   # Model factories for testing
│   ├── InventoryItemFactory.php
│   ├── StockFactory.php
│   ├── StockTransferFactory.php
│   ├── UserFactory.php
│   └── WarehouseFactory.php
├── migrations/                  # Database schema migrations
│   ├── 2025_08_02_171237_create_warehouses_table.php
│   ├── 2025_08_02_171247_create_inventory_items_table.php
│   ├── 2025_08_02_171256_create_stocks_table.php
│   └── 2025_08_02_171311_create_stock_transfers_table.php
└── seeders/                     # Database seeders
    └── DatabaseSeeder.php

tests/
├── Feature/                     # Integration/Feature tests
│   └── ExampleTest.php
└── Unit/                        # Unit tests
    └── ExampleTest.php

routes/
└── api.php                      # API route definitions
```
