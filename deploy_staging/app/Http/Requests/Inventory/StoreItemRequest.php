<?php

namespace App\Http\Requests\Inventory;

use App\Enums\ItemType;
use App\Models\Item;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', Item::class);
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'sku' => ['required', 'string', 'max:100', Rule::unique('items', 'sku')],
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', Rule::enum(ItemType::class)],
            'unit' => ['required', 'string', 'max:50'],
            'location' => ['nullable', 'string', 'max:255'],
            'min_stock' => ['nullable', 'integer', 'min:0'],
        ];
    }
}
