<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SantriRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $santriId = $this->route('santri') ? $this->route('santri')->id : null;

        return [
            'nis' => 'required|string|unique:santris,nis,' . $santriId,
            'nisn' => 'nullable|string|unique:santris,nisn,' . $santriId,
            'nama_lengkap' => 'required|string|max:255',
            'jenis_kelamin' => 'required|in:L,P',
            'tempat_lahir' => 'nullable|string|max:255',
            'tanggal_lahir' => 'nullable|date',
            'alamat' => 'nullable|string',
            'no_hp' => 'nullable|string|max:20',
            'foto' => 'nullable|string',
            'kelas_id' => 'nullable|exists:kelas,id',
            'jurusan_id' => 'nullable|exists:jurusans,id',
            'kamar_id' => 'nullable|exists:kamars,id',
            'status_santri' => 'required|string|in:aktif,cuti,lulus,pindah',
            'catatan' => 'nullable|string',
        ];
    }
}
