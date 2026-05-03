<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ObatRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $obatId = $this->route('obat') ? $this->route('obat')->id : null;

        return [
            'kode_obat' => 'required|string|unique:obats,kode_obat,' . $obatId,
            'nama_obat' => 'required|string|max:255',
            'kategori' => 'required|string|max:100',
            'bentuk_sediaan' => 'required|string|max:100',
            'satuan' => 'required|string|max:100',
            // Stok awal hanya saat create. Update stok lewat mutasi.
            'stok' => $this->isMethod('post') ? 'required|integer|min:0' : 'prohibited',
            'stok_minimum' => 'required|integer|min:0',
            'tanggal_kadaluarsa' => 'required|date',
            'nomor_batch' => 'nullable|string|max:255',
            'lokasi_penyimpanan' => 'nullable|string|max:255',
            'deskripsi' => 'nullable|string',
        ];
    }

    public function messages(): array
    {
        return [
            'stok.prohibited' => 'Stok tidak boleh diubah langsung. Gunakan fitur Mutasi Stok.',
        ];
    }
}
