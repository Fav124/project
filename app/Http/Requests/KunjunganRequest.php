<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class KunjunganRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'santri_id' => 'required|exists:santris,id',
            'tanggal_kunjungan' => 'required|date',
            'keluhan_utama' => 'required|string|max:255',
            'riwayat_keluhan' => 'nullable|string',
            'suhu' => 'nullable|numeric|min:30|max:45',
            'tekanan_darah' => 'nullable|string|max:20',
            'denyut_nadi' => 'nullable|integer|min:0',
            'pernapasan' => 'nullable|integer|min:0',
            'diagnosa_sementara' => 'nullable|string|max:255',
            'tindakan' => 'nullable|string',
            'catatan' => 'nullable|string',
            'status_kunjungan' => 'required|in:baru,dipantau,sembuh,dirujuk,rawat_inap',
        ];
    }
}
