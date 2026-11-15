<?php

namespace App\Http\Requests\Inventory;

use App\Enums\ItemType;
use App\Models\Item;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class UpdateItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        /** @var Item $item */
        $item = $this->route('item');

        return $this->user()->can('update', $item);
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        /** @var Item $item */
        $item = $this->route('item');

        return [
            'sku' => ['required', 'string', 'max:100', Rule::unique('items', 'sku')->ignore($item)],
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', Rule::enum(ItemType::class)],
            'unit' => ['required', 'string', 'max:50'],
            'location' => ['nullable', 'string', 'max:255'],
            'min_stock' => ['nullable', 'integer', 'min:0'],
        ];
    }

    /**
     * @return array<int, callable>
     */
    public function after(): array
    {
        return [
            function (Validator $validator): void {
                /** @var Item $item */
                $item = $this->route('item');
                $type = ItemType::tryFrom((string) $this->input('type'));

                if ($type === null || $type === $item->type) {
                    return;
                }

                if ($item->serialNumbers()->exists()) {
                    $validator->errors()->add(
                        'type',
                        'Jenis barang tidak dapat diubah karena sudah memiliki data serial number.',
                    );
                }
            },
        ];
    }
}
