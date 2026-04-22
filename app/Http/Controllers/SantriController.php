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
        $query = Santri::with(['schoolClass', 'major', 'dormitory']);

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
        $dormitories = \App\Models\Dormitory::orderBy('name')->get();
        $showForm = $request->boolean('create') || $editSantri || $request->isMethod('post');

        // Chart Data
        $genderStats = Santri::select('gender', \DB::raw('count(*) as count'))->groupBy('gender')->get();
        $classStats = SchoolClass::withCount('santris')->get();
        $majorStats = Major::withCount('santris')->get();

        return view('health.santri.index', compact(
            'santris', 'editSantri', 'detailSantri', 'classes', 'majors', 'dormitories', 'showForm',
            'genderStats', 'classStats', 'majorStats'
        ));
    }

    public function show(Santri $santri)
    {
        $santri->load(['schoolClass', 'major']);
        return view('health.santri.show', compact('santri'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'santris' => ['required', 'array', 'min:1'],
            'santris.*.name' => ['required', 'string', 'max:255'],
            'santris.*.nis' => ['nullable', 'string', 'max:50', 'unique:santris,nis'],
            'santris.*.gender' => ['required', 'in:L,P'],
            'santris.*.school_class_id' => ['nullable', 'exists:classes,id'],
            'santris.*.major_id' => ['nullable', 'exists:majors,id'],
            'santris.*.dormitory_id' => ['nullable', 'exists:dormitories,id'],
            'santris.*.dorm_room' => ['nullable', 'string', 'max:100'],
            'santris.*.guardian_name' => ['nullable', 'string', 'max:255'],
            'santris.*.guardian_phone' => ['nullable', 'string', 'max:50'],
        ]);

        foreach ($validated['santris'] as $santriData) {
            Santri::create($santriData);
        }

        $message = count($validated['santris']) . ' data santri berhasil ditambahkan.';
        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => $message]);
        }

        return redirect()->route('santri.index')->with('success', $message);
    }

    public function update(Request $request, Santri $santri)
    {
        $validated = $request->validate($this->santriRules($santri->id));
        $santri->update($validated);

        $message = 'Data santri berhasil diperbarui.';
        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => $message]);
        }

        return redirect()->route('santri.index')->with('success', $message);
    }

    public function destroy(Santri $santri)
    {
        $santri->delete();

        return redirect()->route('santri.index')
            ->with('success', 'Data santri berhasil dihapus.');
    }
}
