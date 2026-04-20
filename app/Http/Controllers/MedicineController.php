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

        $medicines = $query->orderBy('name')->paginate(10)->withQueryString();
        $editMedicine = $request->filled('edit')
            ? Medicine::find($request->edit)
            : null;
        $showForm = $request->boolean('create') || $editMedicine || $request->isMethod('post');

        return view('health.medicines.index', compact('medicines', 'editMedicine', 'showForm'));
    }

    public function show(Medicine $medicine)
    {
        return view('health.medicines.show', compact('medicine'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate($this->medicineRules());
        Medicine::create($validated);

        return redirect()->route('medicines.index')
            ->with('success', 'Data obat berhasil ditambahkan.');
    }

    public function update(Request $request, Medicine $medicine)
    {
        $validated = $request->validate($this->medicineRules());
        $medicine->update($validated);

        return redirect()->route('medicines.index')
            ->with('success', 'Data obat berhasil diperbarui.');
    }

    public function destroy(Medicine $medicine)
    {
        $medicine->delete();

        return redirect()->route('medicines.index')
            ->with('success', 'Data obat berhasil dihapus.');
    }
}
