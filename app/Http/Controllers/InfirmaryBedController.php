<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\HealthManagementValidation;
use App\Models\InfirmaryBed;
use Illuminate\Http\Request;

class InfirmaryBedController extends Controller
{
    use HealthManagementValidation;

    public function index(Request $request)
    {
        $query = InfirmaryBed::query();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($builder) use ($search) {
                $builder->where('code', 'like', '%' . $search . '%')
                    ->orWhere('room_name', 'like', '%' . $search . '%')
                    ->orWhere('occupant_name', 'like', '%' . $search . '%');
            });
        }

        $beds = $query->orderBy('room_name')->orderBy('code')->paginate(10)->withQueryString();
        $editBed = $request->filled('edit')
            ? InfirmaryBed::find($request->edit)
            : null;
        $detailBed = $request->filled('detail')
            ? InfirmaryBed::find($request->detail)
            : null;
        $showForm = $request->boolean('create') || $editBed || $request->isMethod('post');

        return view('health.beds.index', compact('beds', 'editBed', 'detailBed', 'showForm'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'beds' => ['required', 'array', 'min:1'],
            'beds.*.code' => ['required', 'string', 'max:50', 'unique:infirmary_beds,code'],
            'beds.*.room_name' => ['required', 'string', 'max:100'],
            'beds.*.status' => ['required', 'in:available,occupied,maintenance'],
            'beds.*.occupant_name' => ['nullable', 'string', 'max:255'],
            'beds.*.notes' => ['nullable', 'string'],
        ]);

        foreach ($validated['beds'] as $bedData) {
            if ($bedData['status'] !== 'occupied') {
                $bedData['occupant_name'] = null;
            }
            InfirmaryBed::create($bedData);
        }

        $message = count($validated['beds']) . ' data kasur UKS berhasil ditambahkan.';
        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => $message]);
        }

        return redirect()->route('beds.index')->with('success', $message);
    }

    public function update(Request $request, InfirmaryBed $bed)
    {
        $validated = $request->validate($this->bedRules($bed->id));
        $this->normalizeBedOccupant($validated);

        $bed->update($validated);

        $message = 'Data kasur UKS berhasil diperbarui.';
        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => $message]);
        }

        return redirect()->route('beds.index')->with('success', $message);
    }

    public function destroy(InfirmaryBed $bed)
    {
        $bed->delete();

        return redirect()->route('beds.index')
            ->with('success', 'Data kasur UKS berhasil dihapus.');
    }

    private function normalizeBedOccupant(array &$validated): void
    {
        if ($validated['status'] !== 'occupied') {
            $validated['occupant_name'] = null;
        }
    }
}
