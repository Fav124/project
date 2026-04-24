<?php

namespace App\Http\Controllers\Api;

use App\Models\InfirmaryBed;
use App\Models\Medicine;
use App\Models\Santri;
use Illuminate\Http\Request;

class MobileLookupController extends BaseApiController
{
    public function santris(Request $request)
    {
        $query = Santri::with(['schoolClass', 'major', 'dormitory'])->orderBy('name');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($builder) use ($search) {
                $builder->where('name', 'like', '%' . $search . '%')
                    ->orWhere('nis', 'like', '%' . $search . '%');
            });
        }

        $items = $query->limit(50)->get()->map(fn ($santri) => [
            'id' => $santri->id,
            'name' => $santri->name,
            'nis' => $santri->nis,
            'gender' => $santri->gender,
            'class_name' => $santri->schoolClass?->name,
            'major_name' => $santri->major?->name,
            'dormitory_name' => $santri->dormitory?->name,
            'guardian_name' => $santri->guardian_name,
            'guardian_phone' => $santri->guardian_phone,
        ]);

        return $this->success(['items' => $items]);
    }

    public function medicines()
    {
        $items = Medicine::orderBy('name')->get()->map(fn ($medicine) => [
            'id' => $medicine->id,
            'name' => $medicine->name,
            'unit' => $medicine->unit,
            'stock' => $medicine->stock,
            'minimum_stock' => $medicine->minimum_stock,
            'expiry_date' => optional($medicine->expiry_date)->toDateString(),
        ]);

        return $this->success(['items' => $items]);
    }

    public function beds()
    {
        $items = InfirmaryBed::orderBy('code')->get()->map(fn ($bed) => [
            'id' => $bed->id,
            'code' => $bed->code,
            'room_name' => $bed->room_name,
            'status' => $bed->status,
            'occupant_name' => $bed->occupant_name,
        ]);

        return $this->success(['items' => $items]);
    }
}
