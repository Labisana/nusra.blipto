<?php

namespace App\Http\Requests\Bts;

use App\Enums\BtsStatus;
use App\Models\BtsBsc;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreBtsBscRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', BtsBsc::class);
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'nama' => ['required', 'string', 'max:255', Rule::unique('bts_bsc', 'nama')],
            'alamat' => ['required', 'string', 'max:2000'],
            'status' => ['required', Rule::in(BtsStatus::values())],
        ];
    }
}
