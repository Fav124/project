<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Medicine;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MedicineApiController extends Controller
{
    public function index(Request $request)
    {
        $query = Medicine::query();

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        if ($request->boolean('low_stock')) {
            $query->whereColumn('stock', '<=', 'minimum_stock');
        }

        if ($request->boolean('expired')) {
            $query->where('expiry_date', '<', now());
        }

        if ($request->boolean('expiring_soon')) {
            $query->whereBetween('expiry_date', [now(), now()->addMonths(3)]);
        }

        $medicines = $query->orderBy('name')
            ->paginate((int) $request->input('per_page', 20));

        return response()->json([
            'success' => true,
            'data'    => $medicines->map(fn($m) => $this->format($m)),
            'meta'    => ['current_page' => $medicines->currentPage(), 'last_page' => $medicines->lastPage(), 'total' => $medicines->total()],
        ]);
    }

    public function show($id)
    {
        $medicine = Medicine::findOrFail($id);
        return response()->json(['success' => true, 'data' => $this->format($medicine)]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'          => 'required|string|max:255',
            'unit'          => 'required|string|max:50',
            'stock'         => 'required|integer|min:0',
            'minimum_stock' => 'required|integer|min:0',
            'expiry_date'   => 'nullable|date',
            'description'   => 'nullable|string',
        ]);

        $medicine = Medicine::create($validated);

        return response()->json(['success' => true, 'message' => 'Obat berhasil ditambahkan.', 'data' => $this->format($medicine)], 201);
    }

    public function update(Request $request, $id)
    {
        $medicine = Medicine::findOrFail($id);
        $validated = $request->validate([
            'name'          => 'required|string|max:255',
            'unit'          => 'required|string|max:50',
            'stock'         => 'required|integer|min:0',
            'minimum_stock' => 'required|integer|min:0',
            'expiry_date'   => 'nullable|date',
            'description'   => 'nullable|string',
        ]);

        $medicine->update($validated);
        return response()->json(['success' => true, 'message' => 'Data obat diperbarui.', 'data' => $this->format($medicine)]);
    }

    public function destroy($id)
    {
        Medicine::findOrFail($id)->delete();
        return response()->json(['success' => true, 'message' => 'Obat berhasil dihapus.']);
    }

    private function format(Medicine $m): array
    {
        $now = now();
        $status = 'aman';
        if ($m->expiry_date && $m->expiry_date < $now) $status = 'kadaluarsa';
        elseif ($m->expiry_date && $m->expiry_date < $now->copy()->addMonths(3)) $status = 'segera_kadaluarsa';
        elseif ($m->stock <= $m->minimum_stock) $status = 'stok_kritis';

        return [
            'id'            => $m->id,
            'name'          => $m->name,
            'unit'          => $m->unit,
            'stock'         => $m->stock,
            'minimum_stock' => $m->minimum_stock,
            'expiry_date'   => $m->expiry_date?->toDateString(),
            'description'   => $m->description,
            'status'        => $status,
        ];
    }
}
