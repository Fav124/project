<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Santri;
use Illuminate\Http\Request;

class MedicalHistoryController extends Controller
{
    public function show(Santri $santri)
    {
        $santri->load(['kelas', 'kesehatan']);
        
        $history = $santri->kunjungans()
            ->with(['petugas', 'pemberianObats.obat', 'rawatInap'])
            ->latest('tanggal_kunjungan')
            ->get();

        return view('medical-history.show', compact('santri', 'history'));
    }
}
