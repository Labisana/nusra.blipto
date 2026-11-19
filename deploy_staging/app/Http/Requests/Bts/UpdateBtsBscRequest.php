<?php

namespace App\Http\Requests\Bts;

use App\Enums\BtsStatus;
use App\Models\BtsBsc;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateBtsBscRequest extends FormRequest
{
    public function authorize(): bool
    {
        /** @var BtsBsc $btsBsc */
        $btsBsc = $this->route('bts_bsc');

        return $this->user()->can('update', $btsBsc);
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        /** @var BtsBsc $btsBsc */
        $btsBsc = $this->route('bts_bsc');

        return [
            'nama' => ['required', 'string', 'max:255', Rule::unique('bts_bsc', 'nama')->ignore($btsBsc)],
            'alamat' => ['required', 'string', 'max:2000'],
            'status' => ['required', Rule::in(BtsStatus::values())],
        ];
    }
}
