<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RawatInapRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'santri_id' => 'required|exists:santris,id',
            'kunjungan_id' => 'nullable|exists:kunjungans,id',
            'tanggal_masuk' => 'required|date',
            'alasan_rawat' => 'required|string',
            'kondisi_masuk' => 'required|string',
            'catatan' => 'nullable|string',
        ];
    }
}
