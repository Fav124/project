<?php

namespace App\Http\Controllers\Api;

use App\Models\Medicine;
use App\Models\Santri;
use Illuminate\Http\Request;

class MobileLookupController extends BaseApiController
{
    public function santris(Request $request)
    {
        $query = Santri::with(['schoolClass', 'major'])->orderBy('name');

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


}
