<?php

namespace App\Http\Controllers\Api;

use App\Models\Guardian;
use App\Models\Santri;
use Illuminate\Http\Request;
use App\Services\WhatsAppService;

class SantriApiController extends BaseApiController
{
    // ─── Lookups ──────────────────────────────────────────────────────────────

    public function lookups()
    {
        return $this->success([
            'classes'     => \App\Models\SchoolClass::orderBy('name')->get(['id', 'name']),
            'majors'      => \App\Models\Major::orderBy('name')->get(['id', 'name']),
            'dormitories' => [],
        ]);
    }

    // ─── Santri CRUD ──────────────────────────────────────────────────────────

    public function index(Request $request)
    {
        $query = Santri::with(['schoolClass:id,name', 'major:id,name', 'guardians']);

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(fn($b) => $b->where('name', 'like', "%$s%")->orWhere('nis', 'like', "%$s%"));
        }
        if ($request->filled('gender'))       $query->where('gender', $request->gender);
        if ($request->filled('class_id'))     $query->where('class_id', $request->class_id);
        if ($request->filled('major_id'))     $query->where('major_id', $request->major_id);

        $santris = $query->orderBy('name')->paginate((int) $request->input('per_page', 20));

        return response()->json([
            'success' => true,
            'data'    => $santris->map(fn($s) => $this->format($s)),
            'meta'    => [
                'current_page' => $santris->currentPage(),
                'last_page'    => $santris->lastPage(),
                'total'        => $santris->total(),
            ],
        ]);
    }

    public function show($id)
    {
        $santri = Santri::with([
            'schoolClass', 'major',
            'guardians',
            'sicknessCases' => fn($q) => $q->latest('visit_date')->take(5),
            'hospitalReferrals' => fn($q) => $q->latest('referral_date')->take(3),
        ])->findOrFail($id);

        return $this->success($this->formatDetail($santri));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'              => 'required|string|max:255',
            'nis'               => 'nullable|string|unique:santris,nis|max:50',
            'gender'            => 'required|in:L,P',
            'birth_place'       => 'nullable|string|max:100',
            'birth_date'        => 'nullable|date',
            'class_id'          => 'nullable',
            'major_id'          => 'nullable|exists:majors,id',
            'dorm_room'         => 'nullable|string|max:50',
            'blood_type'        => 'nullable|string|max:5',
            'allergies'         => 'nullable|string',
            'medical_history'   => 'nullable|string',
            'special_condition' => 'nullable|string',
            'height'            => 'nullable|numeric',
            'weight'            => 'nullable|numeric',
            'blood_pressure'    => 'nullable|string|max:20',
            'guardian_name'         => 'nullable|string|max:100',
            'guardian_phone'        => 'nullable|string|max:20',
            'guardian_relationship' => 'nullable|string|max:100',
            'guardian_job'          => 'nullable|string|max:100',
            'guardian_address'      => 'nullable|string',
            'notes'             => 'nullable|string',
        ]);

        $santri = Santri::create($validated);
        return $this->success(
            $this->format($santri->load(['schoolClass', 'major'])),
            'Santri berhasil ditambahkan.',
            201
        );
    }

    public function update(Request $request, $id)
    {
        $santri = Santri::findOrFail($id);
        $validated = $request->validate([
            'name'              => 'required|string|max:255',
            'nis'               => 'nullable|string|unique:santris,nis,' . $id . '|max:50',
            'gender'            => 'required|in:L,P',
            'birth_place'       => 'nullable|string|max:100',
            'birth_date'        => 'nullable|date',
            'class_id'          => 'nullable',
            'major_id'          => 'nullable|exists:majors,id',
            'dorm_room'         => 'nullable|string|max:50',
            'blood_type'        => 'nullable|string|max:5',
            'allergies'         => 'nullable|string',
            'medical_history'   => 'nullable|string',
            'special_condition' => 'nullable|string',
            'height'            => 'nullable|numeric',
            'weight'            => 'nullable|numeric',
            'blood_pressure'    => 'nullable|string|max:20',
            'guardian_name'         => 'nullable|string|max:100',
            'guardian_phone'        => 'nullable|string|max:20',
            'guardian_relationship' => 'nullable|string|max:100',
            'guardian_job'          => 'nullable|string|max:100',
            'guardian_address'      => 'nullable|string',
            'notes'             => 'nullable|string',
        ]);

        $santri->update($validated);
        return $this->success(
            $this->format($santri->load(['schoolClass', 'major'])),
            'Data santri diperbarui.'
        );
    }

    public function destroy($id)
    {
        Santri::findOrFail($id)->delete();
        return $this->success([], 'Santri berhasil dihapus.');
    }

    // ─── Guardian CRUD ────────────────────────────────────────────────────────

    public function guardians($santriId)
    {
        $santri = Santri::findOrFail($santriId);
        return $this->success($santri->guardians->map(fn($g) => $this->formatGuardian($g))->values()->toArray());
    }

    public function addGuardian(Request $request, $santriId)
    {
        $santri = Santri::findOrFail($santriId);

        $validated = $request->validate([
            'name'         => 'required|string|max:255',
            'relationship' => 'required|string|max:100',
            'phone'        => 'required|string|max:20',
            'address'      => 'nullable|string',
            'job'          => 'nullable|string|max:100',
            'is_primary'   => 'nullable|boolean',
            'notes'        => 'nullable|string',
        ]);

        if (!empty($validated['is_primary'])) {
            $santri->guardians()->update(['is_primary' => false]);
        }

        $guardian = $santri->guardians()->create($validated);

        return $this->success($this->formatGuardian($guardian), 'Wali berhasil ditambahkan.', 201);
    }

    public function updateGuardian(Request $request, $santriId, $guardianId)
    {
        $santri   = Santri::findOrFail($santriId);
        $guardian = $santri->guardians()->findOrFail($guardianId);

        $validated = $request->validate([
            'name'         => 'required|string|max:255',
            'relationship' => 'required|string|max:100',
            'phone'        => 'required|string|max:20',
            'address'      => 'nullable|string',
            'job'          => 'nullable|string|max:100',
            'is_primary'   => 'nullable|boolean',
            'notes'        => 'nullable|string',
        ]);

        if (!empty($validated['is_primary'])) {
            $santri->guardians()->where('id', '!=', $guardianId)->update(['is_primary' => false]);
        }

        $guardian->update($validated);

        return $this->success($this->formatGuardian($guardian->fresh()), 'Data wali diperbarui.');
    }

    public function destroyGuardian($santriId, $guardianId)
    {
        $santri   = Santri::findOrFail($santriId);
        $guardian = $santri->guardians()->findOrFail($guardianId);
        $guardian->delete();

        return $this->success([], 'Wali berhasil dihapus.');
    }

    public function notifyGuardian(Request $request, $santriId, $guardianId, WhatsAppService $whatsApp)
    {
        $santri   = Santri::findOrFail($santriId);
        $guardian = $santri->guardians()->findOrFail($guardianId);

        if (!$guardian->phone) {
            return $this->error('Nomor HP wali tidak ditemukan.', 422);
        }

        $message = "Assalamualaikum Bapak/Ibu *{$guardian->name}*,\n\n"
            . "Kami dari UKS Pondok Pesantren ingin menyampaikan informasi mengenai santri *{$santri->name}*.\n\n"
            . "Mohon segera menghubungi kami untuk informasi lebih lanjut.\n\nJazakumullahu khairan.";

        $whatsApp->sendTextMessage($guardian->phone, $message);

        return $this->success([], 'Notifikasi berhasil dikirim.');
    }

    // ─── Formatters ───────────────────────────────────────────────────────────

    private function format(Santri $s): array
    {
        $guardian = $this->resolvePrimaryGuardian($s);

        return [
            'id'                     => $s->id,
            'name'                   => $s->name,
            'nis'                    => $s->nis,
            'gender'                 => $s->gender,
            'gender_label'           => $s->gender === 'L' ? 'Laki-laki' : 'Perempuan',
            'class'                  => $s->schoolClass?->name,
            'major'                  => $s->major?->name,
            'guardian_name'          => $s->guardian_name ?: $guardian?->name,
            'guardian_phone'         => $s->guardian_phone ?: $guardian?->phone,
            'guardian_relationship'  => $s->guardian_relationship ?: $guardian?->relationship,
            'guardian_address'       => $s->guardian_address ?: $guardian?->address,
            'guardian_job'           => $s->guardian_job ?: $guardian?->job,
        ];
    }

    private function formatDetail(Santri $s): array
    {
        return array_merge($this->format($s), [
            'birth_place'      => $s->birth_place,
            'birth_date'       => $s->birth_date?->toDateString(),
            'blood_type'       => $s->blood_type ?? null,
            'allergies'        => $s->allergies ?? null,
            'medical_history'  => $s->medical_history ?? null,
            'special_condition'=> $s->special_condition ?? null,
            'height'           => $s->height ?? null,
            'weight'           => $s->weight ?? null,
            'blood_pressure'   => $s->blood_pressure ?? null,
            'notes'            => $s->notes,
            'guardians'        => $s->guardians->map(fn($g) => $this->formatGuardian($g))->values(),
            'recent_sickness'  => $s->sicknessCases->map(fn($c) => [
                'id'         => $c->id,
                'complaint'  => $c->complaint,
                'status'     => $c->status,
                'visit_date' => $c->visit_date?->toDateString(),
            ]),
            'recent_referrals' => $s->hospitalReferrals->map(fn($r) => [
                'id'            => $r->id,
                'hospital_name' => $r->hospital_name,
                'referral_date' => \Carbon\Carbon::parse($r->referral_date)->toDateString(),
                'status'        => $r->status,
            ]),
        ]);
    }

    private function formatGuardian(Guardian $g): array
    {
        return [
            'id'           => $g->id,
            'name'         => $g->name,
            'relationship' => $g->relationship,
            'phone'        => $g->phone,
            'address'      => $g->address,
            'job'          => $g->job,
            'is_primary'   => (bool) $g->is_primary,
            'notes'        => $g->notes,
        ];
    }

    private function resolvePrimaryGuardian(Santri $s): ?Guardian
    {
        if (!$s->relationLoaded('guardians') || $s->guardians->isEmpty()) {
            return null;
        }

        return $s->guardians->firstWhere('is_primary', true) ?? $s->guardians->first();
    }
}
