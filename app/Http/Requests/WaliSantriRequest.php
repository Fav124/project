<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class WaliSantriRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'santri_id' => 'required|exists:santris,id',
            'nama_wali' => 'required|string|max:255',
            'hubungan_wali' => 'required|string|max:100',
            'pekerjaan' => 'nullable|string|max:255',
            'no_hp' => 'nullable|string|max:20',
            'alamat' => 'nullable|string',
            'catatan' => 'nullable|string',
        ];
    }
}
