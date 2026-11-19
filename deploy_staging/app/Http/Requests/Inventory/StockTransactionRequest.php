<?php

namespace App\Http\Requests\Inventory;

use App\Enums\TransactionActivity;
use App\Enums\TransactionDirection;
use App\Models\Item;
use App\Models\StockTransaction;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

abstract class StockTransactionRequest extends FormRequest
{
    abstract protected function direction(): TransactionDirection;

    public function authorize(): bool
    {
        $transaction = $this->route('transaction');

        if ($transaction instanceof StockTransaction) {
            return $this->user()->can('update', $transaction);
        }

        return $this->user()->can('create', StockTransaction::class);
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'activity' => ['required', Rule::in(TransactionActivity::valuesFor($this->direction()))],
            'customer_id' => [
                'nullable',
                'integer',
                Rule::requiredIf(fn (): bool => $this->activityRequiresCustomer()),
                Rule::exists('customers', 'id'),
            ],
            'transaction_date' => ['required', 'date'],
            'reference' => ['nullable', 'string', 'max:100'],
            'note' => ['nullable', 'string', 'max:1000'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.item_id' => ['required', 'integer', 'distinct', Rule::exists('items', 'id')],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
            'items.*.serials' => ['nullable', 'array'],
            'items.*.serials.*' => ['string', 'max:191', 'distinct'],
        ];
    }

    protected function prepareForValidation(): void
    {
        if ($this->filled('reference')) {
            $this->merge(['reference' => trim((string) $this->input('reference'))]);
        }

        $items = array_map(function (array $line): array {
            $line['serials'] = $this->serialsOf($line);

            return $line;
        }, $this->inputLines());

        $this->merge(['items' => $items]);
    }

    /**
     * @return array<int, callable>
     */
    public function after(): array
    {
        return [
            function (Validator $validator): void {
                if ($validator->errors()->isNotEmpty()) {
                    return;
                }

                $lines = $this->inputLines();
                $items = Item::query()
                    ->whereIn('id', array_column($lines, 'item_id'))
                    ->get()
                    ->keyBy('id');

                $allSerials = [];

                foreach ($lines as $index => $line) {
                    $item = $items->get((int) ($line['item_id'] ?? 0));

                    if (! $item instanceof Item) {
                        continue;
                    }

                    $serials = $this->serialsOf($line);
                    $quantity = (int) ($line['quantity'] ?? 0);

                    if ($item->isSerial() && count($serials) !== $quantity) {
                        $validator->errors()->add(
                            "items.{$index}.serials",
                            "Jumlah serial number untuk {$item->name} harus sama dengan Qty ({$quantity}).",
                        );
                    }

                    if (! $item->isSerial() && $serials !== []) {
                        $validator->errors()->add(
                            "items.{$index}.serials",
                            'Barang non-serial tidak menggunakan serial number.',
                        );
                    }

                    foreach ($serials as $serial) {
                        if (in_array($serial, $allSerials, true)) {
                            $validator->errors()->add(
                                "items.{$index}.serials",
                                "Serial number {$serial} dipilih lebih dari sekali.",
                            );
                        }

                        $allSerials[] = $serial;
                    }
                }
            },
        ];
    }

    /**
     * Baris barang yang dikirim dari form, sudah dipastikan berbentuk array.
     *
     * @return array<int, array<string, mixed>>
     */
    protected function inputLines(): array
    {
        $lines = $this->input('items', []);

        if (! is_array($lines)) {
            return [];
        }

        return array_values(array_filter($lines, is_array(...)));
    }

    /**
     * @param  array<string, mixed>  $line
     * @return array<int, string>
     */
    protected function serialsOf(array $line): array
    {
        $serials = $line['serials'] ?? [];

        if (is_string($serials)) {
            $serials = preg_split('/[\r\n,;]+/', $serials) ?: [];
        }

        if (! is_array($serials)) {
            return [];
        }

        return collect($serials)
            ->map(fn (mixed $serial): string => trim((string) $serial))
            ->filter(fn (string $serial): bool => $serial !== '')
            ->unique()
            ->values()
            ->all();
    }

    public function messages(): array
    {
        return [
            'customer_id.required' => 'Pelanggan wajib dipilih untuk aktivitas ini.',
            'customer_id.exists' => 'Pelanggan yang dipilih tidak valid.',
            'items.required' => 'Minimal satu barang harus diisi.',
            'items.*.item_id.distinct' => 'Barang yang sama tidak boleh dipilih lebih dari satu baris.',
            'items.*.serials.*.distinct' => 'Serial number tidak boleh duplikat.',
        ];
    }

    protected function activityRequiresCustomer(): bool
    {
        $activity = TransactionActivity::tryFrom((string) $this->input('activity'));

        return $activity?->requiresCustomer() ?? false;
    }
}
