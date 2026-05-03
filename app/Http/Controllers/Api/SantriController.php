<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Jurusan;
use App\Models\Kamar;
use App\Models\Kelas;
use App\Models\Santri;
use App\Models\WaliSantri;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SantriController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Santri::with(['kelas', 'jurusan', 'kamar', 'kesehatan', 'waliSantris', 'kunjungans' => function($q) {
            $q->latest()->take(3);
        }]);

        if ($request->search) {
            $query->where('nama_lengkap', 'like', '%' . $request->search . '%')
                  ->orWhere('nis', 'like', '%' . $request->search . '%');
        }

        if ($request->status) $query->where('status_santri', $request->status);
        if ($request->class_id) $query->where('kelas_id', $request->class_id);
        if ($request->dormitory_id) $query->where('kamar_id', $request->dormitory_id);
        if ($request->gender) $query->where('jenis_kelamin', $request->gender);

        $paginator = $query->latest()->paginate($request->input('per_page', 20));

        $mappedData = collect($paginator->items())->map(function ($santri) {
            return $this->mapSantriForMobile($santri);
        });

        return response()->json([
            'success' => true,
            'data' => $mappedData,
            'meta' => [
                'current_page' => $paginator->currentPage(),
                'last_page' => $paginator->lastPage(),
                'total' => $paginator->total(),
            ]
        ]);
    }

    private function mapSantriForMobile($santri)
    {
        $wali = $santri->waliSantris->first();
        $health = $santri->kesehatan;
        return [
            'id' => $santri->id,
            'name' => $santri->nama_lengkap,
            'nis' => $santri->nis,
            'gender' => $santri->jenis_kelamin,
            'gender_label' => $santri->jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan',
            'class' => $santri->kelas?->nama_kelas,
            'major' => $santri->jurusan?->nama_jurusan,
            'dormitory' => $santri->kamar?->nama_kamar,
            'dorm_room' => $santri->kamar?->nama_kamar,
            'guardian_name' => $wali?->nama_wali,
            'guardian_relationship' => $wali?->hubungan_wali,
            'guardian_phone' => $wali?->no_hp,
            'guardian_address' => $wali?->alamat,
            'guardian_job' => $wali?->pekerjaan,
            'birth_place' => $santri->tempat_lahir,
            'birth_date' => $santri->tanggal_lahir?->format('Y-m-d'),
            'notes' => $health?->catatan_kesehatan,
            'blood_type' => $health?->golongan_darah,
            'allergies' => $health?->alergi,
            'medical_history' => $health?->riwayat_penyakit,
            'special_condition' => $health?->kondisi_khusus,
            'height' => $health?->tinggi_badan,
            'weight' => $health?->berat_badan,
            'blood_pressure' => $health?->tekanan_darah,
            'recent_sickness' => $santri->kunjungans->map(function($k) {
                return [
                    'id' => $k->id,
                    'complaint' => $k->keluhan_utama,
                    'status' => $k->status_kunjungan,
                    'visit_date' => $k->tanggal_kunjungan->format('Y-m-d H:i'),
                ];
            })
        ];
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'nis' => 'nullable|unique:santris,nis',
            'name' => 'required|string|max:255',
            'gender' => 'required|in:L,P',
            'class_id' => 'required|exists:kelas,id',
            'major_id' => 'nullable|exists:jurusans,id',
            'dormitory_id' => 'nullable|exists:kamars,id',
            'birth_place' => 'nullable|string|max:255',
            'birth_date' => 'nullable|date',
            'notes' => 'nullable|string',
            'blood_type' => 'nullable|string|max:5',
            'allergies' => 'nullable|string',
            'medical_history' => 'nullable|string',
            'special_condition' => 'nullable|string',
            'height' => 'nullable|numeric',
            'weight' => 'nullable|numeric',
            'blood_pressure' => 'nullable|string|max:20',
            // Guardian Data
            'guardian_name' => 'nullable|string|max:255',
            'guardian_relationship' => 'nullable|string|max:100',
            'guardian_phone' => 'nullable|string|max:20',
            'guardian_address' => 'nullable|string',
            'guardian_job' => 'nullable|string|max:255',
        ]);

        try {
            $santri = DB::transaction(function () use ($request) {
                $santri = Santri::create([
                    'nis' => $request->nis,
                    'nama_lengkap' => $request->name,
                    'jenis_kelamin' => $request->gender,
                    'kelas_id' => $request->class_id,
                    'jurusan_id' => $request->major_id,
                    'kamar_id' => $request->dormitory_id,
                    'tempat_lahir' => $request->birth_place,
                    'tanggal_lahir' => $request->birth_date,
                    'status_santri' => 'aktif',
                ]);

                if ($request->notes) {
                    $santri->kesehatan()->updateOrCreate(
                        ['santri_id' => $santri->id],
                        ['catatan_kesehatan' => $request->notes]
                    );
                }

                if ($request->guardian_name) {
                    WaliSantri::create([
                        'santri_id' => $santri->id,
                        'nama_wali' => $request->guardian_name,
                        'hubungan_wali' => $request->guardian_relationship,
                        'no_hp' => $request->guardian_phone,
                        'alamat' => $request->guardian_address,
                        'pekerjaan' => $request->guardian_job,
                    ]);
                }

                return $santri;
            });

            return response()->json([
                'success' => true,
                'message' => 'Data santri berhasil ditambahkan.',
                'data' => $this->mapSantriForMobile($santri->load(['kelas', 'jurusan', 'kamar', 'waliSantris', 'kesehatan']))
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menambahkan data santri.',
                'error' => $e->getMessage()
            ], 400);
        }
    }

    public function show(Santri $santri): JsonResponse
    {
        $santri->load(['kelas', 'jurusan', 'kamar', 'waliSantris', 'kesehatan', 'kunjungans' => function($q) {
            $q->latest()->take(5);
        }]);
        return response()->json([
            'success' => true,
            'data' => $this->mapSantriForMobile($santri)
        ]);
    }

    public function update(Request $request, Santri $santri): JsonResponse
    {
        $request->validate([
            'nis' => 'nullable|unique:santris,nis,' . $santri->id,
            'name' => 'required|string|max:255',
            'gender' => 'required|in:L,P',
            'class_id' => 'required|exists:kelas,id',
            'major_id' => 'nullable|exists:jurusans,id',
            'dormitory_id' => 'nullable|exists:kamars,id',
            'birth_place' => 'nullable|string|max:255',
            'birth_date' => 'nullable|date',
            'notes' => 'nullable|string',
            'blood_type' => 'nullable|string|max:5',
            'allergies' => 'nullable|string',
            'medical_history' => 'nullable|string',
            'special_condition' => 'nullable|string',
            'height' => 'nullable|numeric',
            'weight' => 'nullable|numeric',
            'blood_pressure' => 'nullable|string|max:20',
            // Guardian Data
            'guardian_name' => 'nullable|string|max:255',
            'guardian_relationship' => 'nullable|string|max:100',
            'guardian_phone' => 'nullable|string|max:20',
            'guardian_address' => 'nullable|string',
            'guardian_job' => 'nullable|string|max:255',
        ]);

        try {
            DB::transaction(function () use ($request, $santri) {
                $santri->update([
                    'nis' => $request->nis,
                    'nama_lengkap' => $request->name,
                    'jenis_kelamin' => $request->gender,
                    'kelas_id' => $request->class_id,
                    'jurusan_id' => $request->major_id,
                    'kamar_id' => $request->dormitory_id,
                    'tempat_lahir' => $request->birth_place,
                    'tanggal_lahir' => $request->birth_date,
                ]);

                $santri->kesehatan()->updateOrCreate(
                    ['santri_id' => $santri->id],
                    [
                        'catatan_kesehatan' => $request->notes,
                        'golongan_darah' => $request->blood_type,
                        'alergi' => $request->allergies,
                        'riwayat_penyakit' => $request->medical_history,
                        'kondisi_khusus' => $request->special_condition,
                        'tinggi_badan' => $request->height,
                        'berat_badan' => $request->weight,
                        'tekanan_darah' => $request->blood_pressure,
                    ]
                );

                if ($request->guardian_name) {
                    WaliSantri::updateOrCreate(
                        ['santri_id' => $santri->id],
                        [
                            'nama_wali' => $request->guardian_name,
                            'hubungan_wali' => $request->guardian_relationship,
                            'no_hp' => $request->guardian_phone,
                            'alamat' => $request->guardian_address,
                            'pekerjaan' => $request->guardian_job,
                        ]
                    );
                }
            });

            return response()->json([
                'success' => true,
                'message' => 'Data santri berhasil diperbarui.',
                'data' => $this->mapSantriForMobile($santri->load(['kelas', 'jurusan', 'kamar', 'waliSantris']))
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui data santri.',
                'error' => $e->getMessage()
            ], 400);
        }
    }

    public function destroy(Santri $santri): JsonResponse
    {
        $santri->delete();
        return response()->json([
            'success' => true,
            'message' => 'Data santri berhasil dihapus.'
        ]);
    }

    public function lookups(): JsonResponse
    {
        $classes = Kelas::select('id', 'nama_kelas as name')->get();
        $majors = Jurusan::select('id', 'nama_jurusan as name')->get();
        $dormitories = Kamar::select('id', 'nama_kamar as name')->get();

        return response()->json([
            'success' => true,
            'data' => [
                'classes' => $classes,
                'majors' => $majors,
                'dormitories' => $dormitories,
            ]
        ]);
    }
}
