<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Santri;
use Illuminate\Http\Request;

class SantriApiController extends Controller
{
    public function lookups()
    {
        return response()->json([
            'success' => true,
            'data'    => [
                'classes'     => \App\Models\SchoolClass::orderBy('name')->get(['id', 'name']),
                'majors'      => \App\Models\Major::orderBy('name')->get(['id', 'name']),
                'dormitories' => \App\Models\Dormitory::orderBy('name')->get(['id', 'name']),
            ]
        ]);
    }

    public function index(Request $request)
    {
        $query = Santri::with(['schoolClass:id,name', 'major:id,name', 'dormitory:id,name']);

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(fn($b) => $b->where('name', 'like', "%$s%")->orWhere('nis', 'like', "%$s%"));
        }
        if ($request->filled('gender'))     $query->where('gender', $request->gender);
        if ($request->filled('class_id'))   $query->where('class_id', $request->class_id);
        if ($request->filled('major_id'))   $query->where('major_id', $request->major_id);
        if ($request->filled('dormitory_id')) $query->where('dormitory_id', $request->dormitory_id);

        $santris = $query->orderBy('name')->paginate((int) $request->input('per_page', 20));

        return response()->json([
            'success' => true,
            'data'    => $santris->map(fn($s) => $this->format($s)),
            'meta'    => ['current_page' => $santris->currentPage(), 'last_page' => $santris->lastPage(), 'total' => $santris->total()],
        ]);
    }

    public function show($id)
    {
        $santri = Santri::with(['schoolClass', 'major', 'dormitory',
            'sicknessCases' => fn($q) => $q->latest('visit_date')->take(5),
            'hospitalReferrals' => fn($q) => $q->latest('referral_date')->take(3),
        ])->findOrFail($id);

        return response()->json(['success' => true, 'data' => $this->formatDetail($santri)]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'          => 'required|string|max:255',
            'nis'           => 'nullable|string|unique:santris,nis|max:50',
            'gender'        => 'required|in:L,P',
            'birth_place'   => 'nullable|string|max:100',
            'birth_date'    => 'nullable|date',
            'class_id'      => 'nullable|exists:school_classes,id',
            'major_id'      => 'nullable|exists:majors,id',
            'dormitory_id'  => 'nullable|exists:dormitories,id',
            'dorm_room'     => 'nullable|string|max:50',
            'guardian_name' => 'nullable|string|max:100',
            'guardian_phone'=> 'nullable|string|max:20',
            'notes'         => 'nullable|string',
        ]);

        $santri = Santri::create($validated);
        return response()->json(['success' => true, 'message' => 'Santri berhasil ditambahkan.', 'data' => $this->format($santri->load(['schoolClass', 'major', 'dormitory']))], 201);
    }

    public function update(Request $request, $id)
    {
        $santri = Santri::findOrFail($id);
        $validated = $request->validate([
            'name'          => 'required|string|max:255',
            'nis'           => 'nullable|string|unique:santris,nis,' . $id . '|max:50',
            'gender'        => 'required|in:L,P',
            'birth_place'   => 'nullable|string|max:100',
            'birth_date'    => 'nullable|date',
            'class_id'      => 'nullable|exists:school_classes,id',
            'major_id'      => 'nullable|exists:majors,id',
            'dormitory_id'  => 'nullable|exists:dormitories,id',
            'dorm_room'     => 'nullable|string|max:50',
            'guardian_name' => 'nullable|string|max:100',
            'guardian_phone'=> 'nullable|string|max:20',
            'notes'         => 'nullable|string',
        ]);

        $santri->update($validated);
        return response()->json(['success' => true, 'message' => 'Data santri diperbarui.', 'data' => $this->format($santri->load(['schoolClass', 'major', 'dormitory']))]);
    }

    public function destroy($id)
    {
        Santri::findOrFail($id)->delete();
        return response()->json(['success' => true, 'message' => 'Santri berhasil dihapus.']);
    }

    private function format(Santri $s): array
    {
        return [
            'id'            => $s->id,
            'name'          => $s->name,
            'nis'           => $s->nis,
            'gender'        => $s->gender,
            'gender_label'  => $s->gender === 'L' ? 'Laki-laki' : 'Perempuan',
            'class'         => $s->schoolClass?->name,
            'major'         => $s->major?->name,
            'dormitory'     => $s->dormitory?->name,
            'dorm_room'     => $s->dorm_room,
            'guardian_name' => $s->guardian_name,
            'guardian_phone'=> $s->guardian_phone,
        ];
    }

    private function formatDetail(Santri $s): array
    {
        return array_merge($this->format($s), [
            'birth_place'      => $s->birth_place,
            'birth_date'       => $s->birth_date?->toDateString(),
            'notes'            => $s->notes,
            'recent_sickness'  => $s->sicknessCases->map(fn($c) => [
                'id' => $c->id, 'complaint' => $c->complaint, 'status' => $c->status,
                'visit_date' => $c->visit_date?->toDateString(),
            ]),
            'recent_referrals' => $s->hospitalReferrals->map(fn($r) => [
                'id' => $r->id, 'hospital_name' => $r->hospital_name,
                'referral_date' => \Carbon\Carbon::parse($r->referral_date)->toDateString(), 'status' => $r->status,
            ]),
        ]);
    }
}
