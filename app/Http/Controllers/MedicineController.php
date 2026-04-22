<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\HealthManagementValidation;
use App\Models\Medicine;
use Illuminate\Http\Request;

class MedicineController extends Controller
{
    use HealthManagementValidation;

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
            $query->where('expiry_date', '>=', now())
                  ->where('expiry_date', '<=', now()->addMonths(3));
        }

        $medicines = $query->orderBy('name')->paginate(10)->withQueryString();
        $editMedicine = $request->filled('edit')
            ? Medicine::find($request->edit)
            : null;
        $detailMedicine = $request->filled('detail')
            ? Medicine::find($request->detail)
            : null;
        $showForm = $request->boolean('create') || $editMedicine || $request->isMethod('post');

        // Chart Data
        $stockStats = Medicine::orderBy('stock', 'desc')->take(10)->get();
        $expiryStats = [
            'expired' => Medicine::where('expiry_date', '<', now())->count(),
            'expiring_soon' => Medicine::whereBetween('expiry_date', [now(), now()->addMonths(3)])->count(),
            'safe' => Medicine::where('expiry_date', '>', now()->addMonths(3))->count(),
        ];
        
        // Usage stats (top 5 most prescribed)
        $usageStats = \DB::table('medicine_sickness_case')
            ->join('medicines', 'medicines.id', '=', 'medicine_sickness_case.medicine_id')
            ->select('medicines.name', \DB::raw('SUM(quantity) as total'))
            ->groupBy('medicines.id', 'medicines.name')
            ->orderBy('total', 'desc')
            ->take(5)
            ->get();

        return view('health.medicines.index', compact(
            'medicines', 'editMedicine', 'detailMedicine', 'showForm',
            'stockStats', 'expiryStats', 'usageStats'
        ));
    }

    public function show(Medicine $medicine)
    {
        return view('health.medicines.show', compact('medicine'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'medicines' => ['required', 'array', 'min:1'],
            'medicines.*.name' => ['required', 'string', 'max:255'],
            'medicines.*.unit' => ['required', 'string', 'max:50'],
            'medicines.*.stock' => ['required', 'integer', 'min:0'],
            'medicines.*.minimum_stock' => ['required', 'integer', 'min:0'],
            'medicines.*.expiry_date' => ['nullable', 'date'],
            'medicines.*.description' => ['nullable', 'string'],
        ]);

        foreach ($validated['medicines'] as $medicineData) {
            Medicine::create($medicineData);
        }

        $message = count($validated['medicines']) . ' data obat berhasil ditambahkan.';
        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => $message]);
        }

        return redirect()->route('medicines.index')->with('success', $message);
    }

    public function update(Request $request, Medicine $medicine)
    {
        $validated = $request->validate($this->medicineRules());
        $medicine->update($validated);

        $message = 'Data obat berhasil diperbarui.';
        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => $message]);
        }

        return redirect()->route('medicines.index')->with('success', $message);
    }

    public function destroy(Medicine $medicine)
    {
        $medicine->delete();

        return redirect()->route('medicines.index')
            ->with('success', 'Data obat berhasil dihapus.');
    }
}
