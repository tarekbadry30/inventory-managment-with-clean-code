# Warehouse Inventory Management API

A RESTful API for managing inventory across multiple warehouses built with Laravel 11. This API provides complete warehouse management, inventory tracking, stock operations, and transfer capabilities with authentication and real-time notifications.

## 🚀 Quick Start

### Base URL
```
http://localhost:8000/api
```

### Authentication
All API endpoints (except auth) require authentication using Laravel Sanctum tokens.

**Headers Required:**
```
Authorization: Bearer {your-token}
Content-Type: application/json
Accept: application/json
```

---

## 📋 API Endpoints

### 🔐 Authentication

#### Register
Create a new user account.
```http
POST /auth/register
```
**Request:**
```json
{
    "name": "John Doe",
    "email": "john@example.com",
    "password": "password123",
    "password_confirmation": "password123"
}
```

#### Login
Authenticate and get access token.
```http
POST /auth/login
```
**Request:**
```json
{
    "email": "john@example.com",
    "password": "password123"
}
```

#### Get Current User
```http
GET /auth/user
```

#### Logout
```http
POST /auth/logout
```

---

### 🏢 Warehouses

#### List Warehouses
```http
GET /warehouses
```
**Query Parameters:**
- `search` - Search by name or location
- `per_page` - Items per page (default: 15, max: 100)

#### Create Warehouse
```http
POST /warehouses
```
**Request:**
```json
{
    "name": "Main Warehouse",
    "location": "123 Storage St, City, State",
    "description": "Primary storage facility"
}
```

#### Update Warehouse
```http
PUT /warehouses/{id}
```

#### Delete Warehouse
```http
DELETE /warehouses/{id}
```

#### Get Warehouse Inventory (Cached)
```http
GET /warehouses/{id}/inventory
```
**Query Parameters:**
- `search` - Search items by name or SKU
- `min_quantity` - Minimum stock quantity
- `max_quantity` - Maximum stock quantity  
- `low_stock_only` - Show only low stock items (true/false)

---

### 📦 Inventory Items

#### List Inventory Items
```http
GET /inventory-items
```
**Query Parameters:**
- `search` - Search by name, SKU, or description
- `min_price` - Minimum price filter
- `max_price` - Maximum price filter
- `per_page` - Items per page (default: 15, max: 100)

#### Create Inventory Item
```http
POST /inventory-items
```
**Request:**
```json
{
    "name": "Laptop Computer",
    "sku": "LAP001",
    "description": "High-performance laptop",
    "price": 999.99,
    "low_stock_threshold": 10
}
```

#### Get Inventory Item
```http
GET /inventory-items/{id}
```

#### Update Inventory Item
```http
PUT /inventory-items/{id}
```

#### Delete Inventory Item
```http
DELETE /inventory-items/{id}
```

---

### 📊 Stock Management

#### List Stocks
```http
GET /stocks
```
**Query Parameters:**
- `warehouse_id` - Filter by warehouse
- `inventory_item_id` - Filter by inventory item
- `min_quantity` - Minimum stock quantity
- `max_quantity` - Maximum stock quantity
- `low_stock_only` - Show only low stock items (true/false)
- `search` - Search by item name, SKU, or warehouse
- `per_page` - Items per page (default: 15, max: 100)

#### Create Stock Record
```http
POST /stocks
```
**Request:**
```json
{
    "warehouse_id": 1,
    "inventory_item_id": 1,
    "quantity": 100
}
```

#### Get Stock Record
```http
GET /stocks/{id}
```

#### Update Stock Record
```http
PUT /stocks/{id}
```
**Request:**
```json
{
    "quantity": 150
}
```

#### Update Stock Quantity Only
```http
PATCH /stocks/{id}/quantity
```
**Request:**
```json
{
    "quantity": 200
}
```

#### Delete Stock Record
```http
DELETE /stocks/{id}
```

---

### 🔄 Stock Transfers

#### List Stock Transfers
```http
GET /stock-transfers
```
**Query Parameters:**
- `status` - Filter by status (pending, completed, cancelled)
- `from_warehouse_id` - Filter by source warehouse
- `to_warehouse_id` - Filter by destination warehouse
- `inventory_item_id` - Filter by inventory item
- `per_page` - Items per page (default: 15, max: 100)

#### Create Stock Transfer
```http
POST /stock-transfers
```
**Request:**
```json
{
    "from_warehouse_id": 1,
    "to_warehouse_id": 2,
    "inventory_item_id": 1,
    "quantity": 25,
    "notes": "Regular restocking transfer"
}
```

#### Get Stock Transfer
```http
GET /stock-transfers/{id}
```

#### Cancel Stock Transfer
```http
POST /stock-transfers/{id}/cancel
```

#### Accept Stock Transfer
```http
POST /stock-transfers/{id}/accept
```

#### Transfer Statistics
```http
GET /stock-transfers-statistics
```

---

## 📝 Response Format

### Success Response
```json
{
    "success": true,
    "message": "Operation successful",
    "data": { ... }
}
```

### Error Response
```json
{
    "success": false,
    "message": "Error description",
    "errors": { ... }
}
```

### Paginated Response
```json
{
    "success": true,
    "data": {
        "items": [...],
        "pagination": {
            "total": 10,
            "count": 50,
            "per_page": 10,
            "current_page": 1,
            "total_pages": 5,
            "next_page_url": null,
            "prev_page_url": null
        }
    },
    "message": ""
}
```

---

## 🔍 HTTP Status Codes

| Code | Description |
|------|-------------|
| 200  | OK - Request successful |
| 201  | Created - Resource created successfully |
| 400  | Bad Request - Invalid request data |
| 401  | Unauthorized - Authentication required |
| 403  | Forbidden - Access denied |
| 404  | Not Found - Resource not found |
| 422  | Unprocessable Entity - Validation failed |
| 500  | Internal Server Error - Server error |

---

## 🛠️ Setup & Installation

### Prerequisites
- PHP 8.1+
- Composer
- MySQL/PostgreSQL/SQLite
- Redis (optional)

### Installation Steps

1. **Clone Repository**
```bash
git clone https://github.com/tarekbadry30/warehouse-inventory-management-task.git
cd warehouse-inventory-management-task
```

2. **Install Dependencies**
```bash
composer install
```

3. **Environment Setup**
```bash
cp .env.example .env
php artisan key:generate
```

4. **Database Setup**
```bash
# Configure database in .env file
php artisan migrate
php artisan db:seed  # Optional: Add test data
```

5. **Start Server**
```bash
php artisan serve
```

6. **Queue Workers (Optional)**
```bash
php artisan queue:work
```
