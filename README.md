# Inventory Management System API

A comprehensive RESTful API for managing inventory across multiple warehouses built with Laravel 10 using enterprise-level architecture patterns.

## 🏗️ Architecture Overview

This application implements a **Clean Architecture** approach with the following layers:

- **Repository Pattern**: Data access abstraction
- **Service Layer**: Business logic encapsulation  
- **DTOs**: Type-safe data transfer objects
- **API Resources**: Consistent response formatting
- **Dependency Injection**: Interface-based programming
- **Event-Driven**: Decoupled notification system

For detailed architecture documentation, see [ARCHITECTURE.md](ARCHITECTURE.md).

## Overview

This Laravel application provides a complete inventory management solution with features for tracking stock across multiple warehouses, handling stock transfers, and monitoring low stock levels with automated notifications.

## Key Features

### 🏢 Multi-Warehouse Management
- Create and manage multiple warehouse locations
- Track inventory levels per warehouse
- Cached inventory retrieval for optimal performance

### 📦 Inventory Item Management
- Complete CRUD operations for inventory items
- Advanced search functionality (name, SKU, price range)
- Efficient pagination for large datasets
- Low stock threshold monitoring

### 🔄 Stock Transfer System
- Transfer stock between warehouses
- Real-time validation of stock availability
- Transfer status tracking (pending, completed, cancelled)
- Comprehensive transfer history and statistics

### 🔔 Event-Driven Notifications
- Automated low stock detection
- Queued email notifications (demo implementation)
- Event-listener architecture for extensibility

### 🧪 Comprehensive Testing
- Unit tests for business logic
- Feature tests for API endpoints
- Event testing for notification system
- 100% coverage of critical functionality

## Technology Stack

- **Framework**: Laravel 10
- **Authentication**: Laravel Sanctum
- **Database**: MySQL/PostgreSQL/SQLite
- **Caching**: Redis/File-based
- **Testing**: PHPUnit
- **Queue System**: Database/Redis
- **Architecture**: Repository + Service Layer Pattern
- **Type Safety**: DTOs and Enums
- **API**: RESTful with Resource transformations

## Database Schema

### Core Models
- **Warehouse**: Storage locations with basic details
- **InventoryItem**: Product metadata (name, SKU, price, thresholds)
- **Stock**: Links items to warehouses with quantities
- **StockTransfer**: Transfer logs between warehouses
- **User**: Authentication and audit trails

### Key Relationships
- Warehouses have many Stocks
- InventoryItems have many Stocks
- StockTransfers belong to warehouses and items
- All transfers are audited with user tracking

## API Endpoints

### Authentication
- `POST /api/auth/register` - User registration
- `POST /api/auth/login` - User login
- `POST /api/auth/logout` - User logout
- `GET /api/auth/user` - Get current user

### Inventory Management
- `GET /api/inventory` - Paginated inventory with search/filters
- `POST /api/inventory-items` - Create inventory item
- `GET /api/inventory-items/{id}` - Get specific item
- `PUT /api/inventory-items/{id}` - Update item
- `DELETE /api/inventory-items/{id}` - Delete item

### Warehouse Management
- `GET /api/warehouses` - List all warehouses
- `POST /api/warehouses` - Create warehouse
- `GET /api/warehouses/{id}` - Get specific warehouse
- `GET /api/warehouses/{id}/inventory` - Get warehouse inventory (cached)
- `PUT /api/warehouses/{id}` - Update warehouse
- `DELETE /api/warehouses/{id}` - Delete warehouse

### Stock Transfers
- `GET /api/stock-transfers` - List transfers with filters
- `POST /api/stock-transfers` - Create new transfer
- `GET /api/stock-transfers/{id}` - Get specific transfer
- `PUT /api/stock-transfers/{id}` - Update transfer (pending only)
- `POST /api/stock-transfers/{id}/cancel` - Cancel transfer
- `GET /api/stock-transfers-statistics` - Transfer statistics


## API Documentation

Detailed API documentation is available  
in [API_README.md](./API_README.md) and  postman collection [inventory-managment-with-clean-code-api.postman_collection](./inventory-managment-with-clean-code-api.postman_collection).


## Setup Instructions

### Prerequisites
- PHP 8.1 or higher
- Composer
- MySQL/PostgreSQL/SQLite
- Redis (optional, for caching and queues)

### Installation

1. **Clone the repository**
   ```bash
   git clone https://github.com/tarekbadry30/inventory-managment-with-clean-code
   cd inventory-managment-with-clean-code
   ```

2. **Install dependencies**
   ```bash
   composer install
   ```

3. **Environment configuration**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Database setup**
   - Configure database settings in `.env`
   - Run migrations:
   ```bash
   php artisan migrate
   ```

5. **Optional: Seed test data**
   ```bash
   php artisan db:seed
   ```

6. **Start the development server**
   ```bash
   php artisan serve
   ```

### Queue Configuration

For production environments, configure queue workers:

```bash
# Start queue worker
php artisan queue:work

# Or use Supervisor for production
```

## Testing

Run the comprehensive test suite:

```bash
# Run all tests
php artisan test

# Run specific test suites
php artisan test --testsuite=Unit
php artisan test --testsuite=Feature

```

### Test Coverage
- **Unit Tests**: Stock update logic, event firing, business rules
- **Feature Tests**: API endpoints, authentication, stock transfers
- **Integration Tests**: Event-listener chains, database transactions

## Project Architecture

Detailed Project Architecture is available in [ARCHITECTURE.md](./ARCHITECTURE.md).


## Performance Considerations

### Caching Strategy
- Warehouse inventory queries are cached for 5 minutes
- Cache invalidation on stock updates
- Redis recommended for production

### Database Optimization
- Composite indexes on stock table
- Foreign key constraints for data integrity
- Optimized pagination queries

### Queue Processing
- Low stock notifications are queued
- Asynchronous processing for better performance
