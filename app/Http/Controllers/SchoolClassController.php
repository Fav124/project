<?php

namespace App\Http\Controllers;

use App\Models\Major;
use App\Models\SchoolClass;
use Illuminate\Http\Request;

class SchoolClassController extends Controller
{
    public function index(Request $request)
    {
        $query = SchoolClass::with('majors')->withCount('santris');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($builder) use ($search) {
                $builder->where('name', 'like', '%' . $search . '%')
                    ->orWhereHas('majors', function ($majorQuery) use ($search) {
                        $majorQuery->where('name', 'like', '%' . $search . '%');
                    });
            });
        }

        $classes = $query->latest()->paginate(10)->withQueryString();
        $majors = Major::orderBy('name')->get();
        $editClass = $request->filled('edit') ? SchoolClass::find($request->edit) : null;
        $detailClass = $request->filled('detail') ? SchoolClass::with(['majors', 'santris'])->find($request->detail) : null;
        $showForm = $request->boolean('create') || $editClass || $request->isMethod('post');

        // Chart Data
        $classStats = SchoolClass::withCount('santris')->get();
        $majorStats = Major::withCount('santris')->get();

        return view('health.classes.index', compact('classes', 'majors', 'editClass', 'detailClass', 'showForm', 'classStats', 'majorStats'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'classes' => ['required', 'array', 'min:1'],
            'classes.*.name' => ['required', 'string', 'max:255', 'unique:classes,name'],
            'classes.*.major_ids' => ['nullable', 'array'],
            'classes.*.major_ids.*' => ['exists:majors,id'],
            'classes.*.description' => ['nullable', 'string'],
        ]);

        foreach ($validated['classes'] as $classData) {
            $class = SchoolClass::create([
                'name' => $classData['name'],
                'description' => $classData['description'] ?? null,
            ]);

            if (isset($classData['major_ids'])) {
                $class->majors()->sync($classData['major_ids']);
            }
        }

        $message = count($validated['classes']) . ' data kelas berhasil ditambahkan.';
        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => $message]);
        }

        return redirect()->route('classes.index')->with('success', $message);
    }

    public function update(Request $request, SchoolClass $class)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:classes,name,' . $class->id],
            'major_ids' => ['nullable', 'array'],
            'major_ids.*' => ['exists:majors,id'],
            'description' => ['nullable', 'string'],
        ]);

        $class->update([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
        ]);

        if (isset($validated['major_ids'])) {
            $class->majors()->sync($validated['major_ids']);
        } else {
            $class->majors()->sync([]);
        }

        $message = 'Data kelas berhasil diperbarui.';
        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => $message]);
        }

        return redirect()->route('classes.index')->with('success', $message);
    }

    public function destroy(SchoolClass $class)
    {
        $class->delete();

        return redirect()->route('classes.index')
            ->with('success', 'Data kelas berhasil dihapus.');
    }
}
