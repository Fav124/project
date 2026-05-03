<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PemberianObatRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'kunjungan_id' => 'required|exists:kunjungans,id',
            'obat_id' => 'required|exists:obats,id',
            'jumlah' => 'required|integer|min:1',
            'dosis' => 'required|string|max:255',
            'aturan_pakai' => 'required|string|max:255',
            'catatan' => 'nullable|string',
        ];
    }
}
