<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MutasiStokRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'obat_id' => 'required|exists:obats,id',
            'jenis_mutasi' => 'required|in:masuk,keluar,penyesuaian,rusak,kadaluarsa,retur',
            'jumlah' => 'required|integer|min:1',
            'catatan' => 'nullable|string',
        ];
    }
}
