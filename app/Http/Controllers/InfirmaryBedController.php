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
        $showForm = $request->boolean('create') || $editBed || $request->isMethod('post');

        return view('health.beds.index', compact('beds', 'editBed', 'showForm'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate($this->bedRules());
        $this->normalizeBedOccupant($validated);

        InfirmaryBed::create($validated);

        return redirect()->route('beds.index')
            ->with('success', 'Data kasur UKS berhasil ditambahkan.');
    }

    public function update(Request $request, InfirmaryBed $bed)
    {
        $validated = $request->validate($this->bedRules($bed->id));
        $this->normalizeBedOccupant($validated);

        $bed->update($validated);

        return redirect()->route('beds.index')
            ->with('success', 'Data kasur UKS berhasil diperbarui.');
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
