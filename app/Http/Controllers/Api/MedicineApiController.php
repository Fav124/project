<?php

namespace App\Http\Controllers\Api;

use App\Models\Medicine;
use App\Models\MedicineMutation;
use Illuminate\Http\Request;

class MedicineApiController extends BaseApiController
{
    public function index(Request $request)
    {
        $query = Medicine::query();

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('kode_obat', 'like', '%' . $request->search . '%');
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
            'meta'    => [
                'current_page' => $medicines->currentPage(),
                'last_page'    => $medicines->lastPage(),
                'total'        => $medicines->total(),
            ],
        ]);
    }

    public function show($id)
    {
        $medicine = Medicine::with(['mutations' => fn($q) => $q->latest()->take(20)])->findOrFail($id);
        return $this->success($this->formatDetail($medicine));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'                => 'required|string|max:255',
            'kode_obat'           => 'nullable|string|max:50|unique:medicines,kode_obat',
            'kategori'            => 'nullable|string|max:100',
            'bentuk_sediaan'      => 'nullable|string|max:100',
            'unit'                => 'required|string|max:50',
            'stock'               => 'required|integer|min:0',
            'minimum_stock'       => 'required|integer|min:0',
            'expiry_date'         => 'nullable|date',
            'lokasi_penyimpanan'  => 'nullable|string|max:100',
            'description'         => 'nullable|string',
        ]);

        $medicine = Medicine::create($validated);

        return $this->success($this->format($medicine), 'Obat berhasil ditambahkan.', 201);
    }

    public function update(Request $request, $id)
    {
        $medicine = Medicine::findOrFail($id);
        $validated = $request->validate([
            'name'                => 'required|string|max:255',
            'kode_obat'           => 'nullable|string|max:50|unique:medicines,kode_obat,' . $id,
            'kategori'            => 'nullable|string|max:100',
            'bentuk_sediaan'      => 'nullable|string|max:100',
            'unit'                => 'required|string|max:50',
            'stock'               => 'required|integer|min:0',
            'minimum_stock'       => 'required|integer|min:0',
            'expiry_date'         => 'nullable|date',
            'lokasi_penyimpanan'  => 'nullable|string|max:100',
            'description'         => 'nullable|string',
        ]);

        $medicine->update($validated);
        return $this->success($this->format($medicine), 'Data obat diperbarui.');
    }

    public function destroy($id)
    {
        Medicine::findOrFail($id)->delete();
        return $this->success([], 'Obat berhasil dihapus.');
    }

    public function recordMutation(Request $request)
    {
        $validated = $request->validate([
            'medicine_id' => 'required|exists:medicines,id',
            'type'        => 'required|in:in,out,adjustment',
            'amount'      => 'required|integer|min:1',
            'notes'       => 'nullable|string',
        ]);

        $medicine = Medicine::findOrFail($validated['medicine_id']);
        $beforeStock = $medicine->stock;

        if ($validated['type'] === 'in') {
            $medicine->increment('stock', $validated['amount']);
        } elseif ($validated['type'] === 'out') {
            if ($medicine->stock < $validated['amount']) {
                return $this->error('Stok tidak mencukupi.', 422);
            }
            $medicine->decrement('stock', $validated['amount']);
        } else {
            // adjustment
            $medicine->update(['stock' => $validated['amount']]);
        }

        $afterStock = $medicine->fresh()->stock;

        MedicineMutation::create([
            'medicine_id'   => $medicine->id,
            'type'          => $validated['type'],
            'amount'        => $validated['amount'],
            'stok_sebelum'  => $beforeStock,
            'stok_sesudah'  => $afterStock,
            'date'          => now()->toDateString(),
            'notes'         => $validated['notes'] ?? null,
            'created_by'    => auth()->id(),
        ]);

        return $this->success([], 'Mutasi stok berhasil dicatat.');
    }

    // ─── Formatters ───────────────────────────────────────────────────────────

    private function format(Medicine $m): array
    {
        $now    = now();
        $status = 'aman';
        if ($m->expiry_date && $m->expiry_date < $now)                    $status = 'kadaluarsa';
        elseif ($m->expiry_date && $m->expiry_date < $now->copy()->addMonths(3)) $status = 'segera_kadaluarsa';
        elseif ($m->stock <= $m->minimum_stock)                            $status = 'stok_kritis';

        return [
            'id'                  => $m->id,
            'kode_obat'           => $m->kode_obat ?? '',
            'name'                => $m->name,
            'kategori'            => $m->kategori ?? '',
            'bentuk_sediaan'      => $m->bentuk_sediaan ?? '',
            'unit'                => $m->unit,
            'stock'               => $m->stock,
            'minimum_stock'       => $m->minimum_stock,
            'expiry_date'         => $m->expiry_date?->toDateString(),
            'lokasi_penyimpanan'  => $m->lokasi_penyimpanan ?? null,
            'description'         => $m->description,
            'status'              => $status,
        ];
    }

    private function formatDetail(Medicine $m): array
    {
        return array_merge($this->format($m), [
            'riwayat_stok' => ($m->mutations ?? collect())->map(fn($mut) => [
                'id'            => $mut->id,
                'type'          => $mut->type,
                'amount'        => $mut->amount,
                'stok_sebelum'  => $mut->stok_sebelum,
                'stok_sesudah'  => $mut->stok_sesudah,
                'date'          => $mut->date,
                'notes'         => $mut->notes,
            ])->values(),
        ]);
    }
}
