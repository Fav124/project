<?php

namespace App\Http\Controllers\Api;

use App\Models\Medicine;
use App\Services\MedicineStockService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

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
            'photo'               => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($request->hasFile('photo')) {
            $path = $request->file('photo')->store('medicines', 'public');
            $validated['photo'] = $path;
        }

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
            'photo'               => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($request->hasFile('photo')) {
            if ($medicine->photo) {
                Storage::disk('public')->delete($medicine->photo);
            }
            $path = $request->file('photo')->store('medicines', 'public');
            $validated['photo'] = $path;
        }

        $medicine->update($validated);
        return $this->success($this->format($medicine), 'Data obat diperbarui.');
    }

    public function destroy($id)
    {
        Medicine::findOrFail($id)->delete();
        return $this->success([], 'Obat berhasil dihapus.');
    }

    public function recordMutation(Request $request, MedicineStockService $stockService)
    {
        $validated = $request->validate([
            'medicine_id' => 'required|exists:medicines,id',
            'type'        => 'required|in:in,out,adjustment',
            'amount'      => 'required|integer|min:1',
            'notes'       => 'nullable|string',
        ]);

        try {
            $stockService->recordMutation(
                Medicine::findOrFail($validated['medicine_id']),
                $validated['type'],
                $validated['amount'],
                $validated['notes'] ?? null
            );
        } catch (\InvalidArgumentException $e) {
            return $this->error($e->getMessage(), 422);
        }

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
            'photo_url'           => $m->photo_url,
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
                'stok_sebelum'  => $mut->before_stock,
                'stok_sesudah'  => $mut->after_stock,
                'date'          => $mut->created_at?->toDateString(),
                'notes'         => $mut->notes,
            ])->values(),
        ]);
    }
}
