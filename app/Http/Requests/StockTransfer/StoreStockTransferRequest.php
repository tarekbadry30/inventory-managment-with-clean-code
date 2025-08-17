<?php

namespace App\Http\Requests\StockTransfer;

use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Gate;
use App\Contracts\StockRepositoryInterface;
use Illuminate\Foundation\Http\FormRequest;

class StoreStockTransferRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Gate::allows('create-transfer');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'from_warehouse_id' => ['required', 'exists:warehouses,id'],
            'to_warehouse_id' => [
                'required',
                'exists:warehouses,id',
                'different:from_warehouse_id'
            ],
            'inventory_item_id' => ['required', 'exists:inventory_items,id'],
            'quantity' => [
                'required',
                'integer',
                'min:1',

            ],
            'notes' => ['nullable', 'string', 'max:1000'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'to_warehouse_id.different' => 'Source and destination warehouses must be different.',
            'from_warehouse_id.exists' => 'The selected source warehouse does not exist.',
            'to_warehouse_id.exists' => 'The selected destination warehouse does not exist.',
            'inventory_item_id.exists' => 'The selected inventory item does not exist.',
        ];
    }
}
