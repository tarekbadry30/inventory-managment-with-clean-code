<?php

namespace App\Http\Requests\Stock;

use Illuminate\Foundation\Http\FormRequest;

class StockIndexRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'search' => 'nullable|string|max:255',
            'warehouse_id' => 'nullable|integer|exists:warehouses,id',
            'inventory_item_id' => 'nullable|integer|exists:inventory_items,id',
            'min_quantity' => 'nullable|integer|min:0',
            'max_quantity' => 'nullable|integer|min:0|gte:min_quantity',
            'low_stock_only' => 'nullable|boolean',
            'per_page' => 'nullable|integer|min:1|max:100',
        ];
    }

    public function messages(): array
    {
        return [
            'warehouse_id.exists' => 'The selected warehouse does not exist.',
            'inventory_item_id.exists' => 'The selected inventory item does not exist.',
            'max_quantity.gte' => 'The maximum quantity must be greater than or equal to minimum quantity.',
            'per_page.max' => 'The items per page cannot exceed 100.',
        ];
    }
}
