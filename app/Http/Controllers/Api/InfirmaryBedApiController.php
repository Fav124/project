<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\InfirmaryBed;
use Illuminate\Http\Request;

class InfirmaryBedApiController extends Controller
{
    public function index(Request $request)
    {
        $query = InfirmaryBed::query();
        if ($request->filled('status')) $query->where('status', $request->status);

        $beds = $query->orderBy('code')->get();
        return response()->json(['success' => true, 'data' => $beds->map(fn($b) => $this->format($b))]);
    }

    public function show($id)
    {
        return response()->json(['success' => true, 'data' => $this->format(InfirmaryBed::findOrFail($id))]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'code'         => 'required|string|unique:infirmary_beds,code|max:20',
            'room_name'    => 'nullable|string|max:100',
            'status'       => 'nullable|in:available,occupied,maintenance',
            'occupant_name'=> 'nullable|string|max:100',
            'notes'        => 'nullable|string',
        ]);
        $bed = InfirmaryBed::create($validated);
        return response()->json(['success' => true, 'message' => 'Kasur UKS ditambahkan.', 'data' => $this->format($bed)], 201);
    }

    public function update(Request $request, $id)
    {
        $bed = InfirmaryBed::findOrFail($id);
        $validated = $request->validate([
            'code'         => 'required|string|unique:infirmary_beds,code,' . $id . '|max:20',
            'room_name'    => 'nullable|string|max:100',
            'status'       => 'required|in:available,occupied,maintenance',
            'occupant_name'=> 'nullable|string|max:100',
            'notes'        => 'nullable|string',
        ]);
        $bed->update($validated);
        return response()->json(['success' => true, 'message' => 'Kasur diperbarui.', 'data' => $this->format($bed)]);
    }

    private function format(InfirmaryBed $b): array
    {
        return [
            'id'            => $b->id,
            'code'          => $b->code,
            'room_name'     => $b->room_name,
            'status'        => $b->status,
            'status_label'  => match($b->status) { 'available' => 'Tersedia', 'occupied' => 'Terpakai', 'maintenance' => 'Perbaikan', default => $b->status },
            'occupant_name' => $b->occupant_name,
            'notes'         => $b->notes,
        ];
    }
}
