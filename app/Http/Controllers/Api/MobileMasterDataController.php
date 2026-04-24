<?php

namespace App\Http\Controllers\Api;

use App\Models\Dormitory;
use App\Models\InfirmaryBed;
use App\Models\Major;
use App\Models\Medicine;
use App\Models\Santri;
use App\Models\SchoolClass;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class MobileMasterDataController extends BaseApiController
{
    public function santris(Request $request)
    {
        $query = Santri::with(['schoolClass', 'major', 'dormitory'])->orderBy('name');

        if ($request->filled('search')) {
            $search = $request->string('search');
            $query->where(function ($builder) use ($search) {
                $builder->where('name', 'like', "%{$search}%")
                    ->orWhere('nis', 'like', "%{$search}%")
                    ->orWhere('guardian_name', 'like', "%{$search}%");
            });
        }

        return $this->success([
            'items' => $query->limit(100)->get()->map(fn (Santri $santri) => $this->transformSantri($santri)),
        ]);
    }

    public function storeSantri(Request $request)
    {
        $this->ensureHealthAccess($request);

        $validated = $request->validate([
            'nis' => ['nullable', 'string', 'max:50', 'unique:santris,nis'],
            'name' => ['required', 'string', 'max:255'],
            'gender' => ['required', 'in:L,P'],
            'birth_place' => ['nullable', 'string', 'max:255'],
            'birth_date' => ['nullable', 'date'],
            'class_id' => ['nullable', 'exists:classes,id'],
            'major_id' => ['nullable', 'exists:majors,id'],
            'dormitory_id' => ['nullable', 'exists:dormitories,id'],
            'class_room' => ['nullable', 'string', 'max:100'],
            'dorm_room' => ['nullable', 'string', 'max:100'],
            'guardian_name' => ['nullable', 'string', 'max:255'],
            'guardian_phone' => ['nullable', 'string', 'max:50'],
            'notes' => ['nullable', 'string'],
        ]);

        $santri = Santri::create($validated)->load(['schoolClass', 'major', 'dormitory']);

        return $this->success([
            'item' => $this->transformSantri($santri),
        ], 'Data santri berhasil ditambahkan.', 201);
    }

    public function updateSantri(Request $request, Santri $santri)
    {
        $this->ensureHealthAccess($request);

        $validated = $request->validate([
            'nis' => ['nullable', 'string', 'max:50', Rule::unique('santris', 'nis')->ignore($santri->id)],
            'name' => ['required', 'string', 'max:255'],
            'gender' => ['required', 'in:L,P'],
            'birth_place' => ['nullable', 'string', 'max:255'],
            'birth_date' => ['nullable', 'date'],
            'class_id' => ['nullable', 'exists:classes,id'],
            'major_id' => ['nullable', 'exists:majors,id'],
            'dormitory_id' => ['nullable', 'exists:dormitories,id'],
            'class_room' => ['nullable', 'string', 'max:100'],
            'dorm_room' => ['nullable', 'string', 'max:100'],
            'guardian_name' => ['nullable', 'string', 'max:255'],
            'guardian_phone' => ['nullable', 'string', 'max:50'],
            'notes' => ['nullable', 'string'],
        ]);

        $santri->update($validated);

        return $this->success([
            'item' => $this->transformSantri($santri->fresh(['schoolClass', 'major', 'dormitory'])),
        ], 'Data santri berhasil diperbarui.');
    }

    public function medicines(Request $request)
    {
        $query = Medicine::orderBy('name');

        if ($request->boolean('low_stock')) {
            $query->whereColumn('stock', '<=', 'minimum_stock');
        }

        return $this->success([
            'items' => $query->get()->map(fn (Medicine $medicine) => $this->transformMedicine($medicine)),
        ]);
    }

    public function storeMedicine(Request $request)
    {
        $this->ensureHealthAccess($request);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'unit' => ['required', 'string', 'max:50'],
            'stock' => ['required', 'integer', 'min:0'],
            'minimum_stock' => ['required', 'integer', 'min:0'],
            'expiry_date' => ['nullable', 'date'],
            'description' => ['nullable', 'string'],
        ]);

        $medicine = Medicine::create($validated);

        return $this->success([
            'item' => $this->transformMedicine($medicine),
        ], 'Data obat berhasil ditambahkan.', 201);
    }

    public function updateMedicine(Request $request, Medicine $medicine)
    {
        $this->ensureHealthAccess($request);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'unit' => ['required', 'string', 'max:50'],
            'stock' => ['required', 'integer', 'min:0'],
            'minimum_stock' => ['required', 'integer', 'min:0'],
            'expiry_date' => ['nullable', 'date'],
            'description' => ['nullable', 'string'],
        ]);

        $medicine->update($validated);

        return $this->success([
            'item' => $this->transformMedicine($medicine->fresh()),
        ], 'Data obat berhasil diperbarui.');
    }

    public function beds(Request $request)
    {
        $query = InfirmaryBed::orderBy('room_name')->orderBy('code');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        return $this->success([
            'items' => $query->get()->map(fn (InfirmaryBed $bed) => $this->transformBed($bed)),
        ]);
    }

    public function storeBed(Request $request)
    {
        $this->ensureHealthAccess($request);

        $validated = $request->validate([
            'code' => ['required', 'string', 'max:50', 'unique:infirmary_beds,code'],
            'room_name' => ['required', 'string', 'max:100'],
            'status' => ['required', 'in:available,occupied,maintenance'],
            'occupant_name' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
        ]);

        if ($validated['status'] !== 'occupied') {
            $validated['occupant_name'] = null;
        }

        $bed = InfirmaryBed::create($validated);

        return $this->success([
            'item' => $this->transformBed($bed),
        ], 'Data kasur UKS berhasil ditambahkan.', 201);
    }

    public function updateBed(Request $request, InfirmaryBed $bed)
    {
        $this->ensureHealthAccess($request);

        $validated = $request->validate([
            'code' => ['required', 'string', 'max:50', Rule::unique('infirmary_beds', 'code')->ignore($bed->id)],
            'room_name' => ['required', 'string', 'max:100'],
            'status' => ['required', 'in:available,occupied,maintenance'],
            'occupant_name' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
        ]);

        if ($validated['status'] !== 'occupied') {
            $validated['occupant_name'] = null;
        }

        $bed->update($validated);

        return $this->success([
            'item' => $this->transformBed($bed->fresh()),
        ], 'Data kasur UKS berhasil diperbarui.');
    }

    public function dormitories()
    {
        return $this->success([
            'items' => Dormitory::withCount('santris')->orderBy('name')->get()->map(fn (Dormitory $dormitory) => [
                'id' => $dormitory->id,
                'name' => $dormitory->name,
                'building' => $dormitory->building,
                'gender' => $dormitory->gender,
                'supervisor_name' => $dormitory->supervisor_name,
                'description' => $dormitory->description,
                'santri_count' => $dormitory->santris_count,
            ]),
        ]);
    }

    public function storeDormitory(Request $request)
    {
        $this->ensureHealthAccess($request);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'building' => ['nullable', 'string', 'max:255'],
            'gender' => ['required', 'in:L,P'],
            'supervisor_name' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
        ]);

        $dormitory = Dormitory::create($validated)->loadCount('santris');

        return $this->success([
            'item' => [
                'id' => $dormitory->id,
                'name' => $dormitory->name,
                'building' => $dormitory->building,
                'gender' => $dormitory->gender,
                'supervisor_name' => $dormitory->supervisor_name,
                'description' => $dormitory->description,
                'santri_count' => $dormitory->santris_count,
            ],
        ], 'Data asrama berhasil ditambahkan.', 201);
    }

    public function updateDormitory(Request $request, Dormitory $dormitory)
    {
        $this->ensureHealthAccess($request);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'building' => ['nullable', 'string', 'max:255'],
            'gender' => ['required', 'in:L,P'],
            'supervisor_name' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
        ]);

        $dormitory->update($validated);
        $dormitory->loadCount('santris');

        return $this->success([
            'item' => [
                'id' => $dormitory->id,
                'name' => $dormitory->name,
                'building' => $dormitory->building,
                'gender' => $dormitory->gender,
                'supervisor_name' => $dormitory->supervisor_name,
                'description' => $dormitory->description,
                'santri_count' => $dormitory->santris_count,
            ],
        ], 'Data asrama berhasil diperbarui.');
    }

    public function classes()
    {
        return $this->success([
            'items' => SchoolClass::with('majors')->orderBy('name')->get()->map(fn (SchoolClass $class) => [
                'id' => $class->id,
                'name' => $class->name,
                'description' => $class->description,
                'major_ids' => $class->majors->pluck('id')->values(),
                'major_names' => $class->majors->pluck('name')->values(),
            ]),
        ]);
    }

    public function majors()
    {
        return $this->success([
            'items' => Major::orderBy('name')->get()->map(fn (Major $major) => [
                'id' => $major->id,
                'name' => $major->name,
                'description' => $major->description,
            ]),
        ]);
    }

    private function transformSantri(Santri $santri): array
    {
        return [
            'id' => $santri->id,
            'nis' => $santri->nis,
            'name' => $santri->name,
            'gender' => $santri->gender,
            'birth_place' => $santri->birth_place,
            'birth_date' => optional($santri->birth_date)->toDateString(),
            'class_id' => $santri->class_id,
            'class_name' => $santri->schoolClass?->name,
            'major_id' => $santri->major_id,
            'major_name' => $santri->major?->name,
            'dormitory_id' => $santri->dormitory_id,
            'dormitory_name' => $santri->dormitory?->name,
            'class_room' => $santri->class_room,
            'dorm_room' => $santri->dorm_room,
            'guardian_name' => $santri->guardian_name,
            'guardian_phone' => $santri->guardian_phone,
            'notes' => $santri->notes,
        ];
    }

    private function transformMedicine(Medicine $medicine): array
    {
        return [
            'id' => $medicine->id,
            'name' => $medicine->name,
            'unit' => $medicine->unit,
            'stock' => $medicine->stock,
            'minimum_stock' => $medicine->minimum_stock,
            'expiry_date' => optional($medicine->expiry_date)->toDateString(),
            'description' => $medicine->description,
            'is_low_stock' => $medicine->stock <= $medicine->minimum_stock,
            'is_expired' => $medicine->isExpired(),
            'is_expiring_soon' => $medicine->isExpiringSoon(),
        ];
    }

    private function transformBed(InfirmaryBed $bed): array
    {
        return [
            'id' => $bed->id,
            'code' => $bed->code,
            'room_name' => $bed->room_name,
            'status' => $bed->status,
            'occupant_name' => $bed->occupant_name,
            'notes' => $bed->notes,
        ];
    }
}
