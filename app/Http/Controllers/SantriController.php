<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\HealthManagementValidation;
use App\Models\Major;
use App\Models\Santri;
use App\Models\SchoolClass;
use Illuminate\Http\Request;

class SantriController extends Controller
{
    use HealthManagementValidation;

    public function index(Request $request)
    {
        $query = Santri::with(['schoolClass', 'major']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($builder) use ($search) {
                $builder->where('name', 'like', '%' . $search . '%')
                    ->orWhere('nis', 'like', '%' . $search . '%')
                    ->orWhere('class_room', 'like', '%' . $search . '%')
                    ->orWhere('dorm_room', 'like', '%' . $search . '%')
                    ->orWhereHas('schoolClass', function ($classQuery) use ($search) {
                        $classQuery->where('name', 'like', '%' . $search . '%');
                    })
                    ->orWhereHas('major', function ($majorQuery) use ($search) {
                        $majorQuery->where('name', 'like', '%' . $search . '%');
                    });
            });
        }

        $santris = $query->latest()->paginate(10)->withQueryString();
        $editSantri = $request->filled('edit')
            ? Santri::find($request->edit)
            : null;
        $detailSantri = $request->filled('detail')
            ? Santri::with(['schoolClass', 'major'])->find($request->detail)
            : null;
            
        $classes = SchoolClass::with('majors')->orderBy('name')->get();
        $majors = Major::orderBy('name')->get();
        $showForm = $request->boolean('create') || $editSantri || $request->isMethod('post');

        return view('health.santri.index', compact('santris', 'editSantri', 'detailSantri', 'classes', 'majors', 'showForm'));
    }

    public function show(Santri $santri)
    {
        $santri->load(['schoolClass', 'major']);
        return view('health.santri.show', compact('santri'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate($this->santriRules());
        Santri::create($validated);

        return redirect()->route('santri.index')
            ->with('success', 'Data santri berhasil ditambahkan.');
    }

    public function update(Request $request, Santri $santri)
    {
        $validated = $request->validate($this->santriRules($santri->id));
        $santri->update($validated);

        return redirect()->route('santri.index')
            ->with('success', 'Data santri berhasil diperbarui.');
    }

    public function destroy(Santri $santri)
    {
        $santri->delete();

        return redirect()->route('santri.index')
            ->with('success', 'Data santri berhasil dihapus.');
    }
}
