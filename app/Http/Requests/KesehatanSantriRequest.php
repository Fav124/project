<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class KesehatanSantriRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'santri_id' => 'required|exists:santris,id',
            'golongan_darah' => 'nullable|string|max:5',
            'alergi' => 'nullable|string',
            'riwayat_penyakit' => 'nullable|string',
            'kondisi_khusus' => 'nullable|string',
            'tinggi_badan' => 'nullable|numeric|min:0',
            'berat_badan' => 'nullable|numeric|min:0',
            'tekanan_darah' => 'nullable|string|max:50',
            'catatan_kesehatan' => 'nullable|string',
        ];
    }
}
