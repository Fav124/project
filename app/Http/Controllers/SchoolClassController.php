<?php

namespace App\Http\Controllers;

use App\Models\Major;
use App\Models\SchoolClass;
use Illuminate\Http\Request;

class SchoolClassController extends Controller
{
    public function index(Request $request)
    {
        $query = SchoolClass::with('majors');

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
        $showForm = $request->boolean('create') || $editClass || $request->isMethod('post');

        return view('health.classes.index', compact('classes', 'majors', 'editClass', 'showForm'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:classes,name'],
            'major_ids' => ['nullable', 'array'],
            'major_ids.*' => ['exists:majors,id'],
            'description' => ['nullable', 'string'],
        ]);

        $class = SchoolClass::create([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
        ]);

        if (isset($validated['major_ids'])) {
            $class->majors()->sync($validated['major_ids']);
        }

        return redirect()->route('classes.index')
            ->with('success', 'Data kelas berhasil ditambahkan.');
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

        return redirect()->route('classes.index')
            ->with('success', 'Data kelas berhasil diperbarui.');
    }

    public function destroy(SchoolClass $class)
    {
        $class->delete();

        return redirect()->route('classes.index')
            ->with('success', 'Data kelas berhasil dihapus.');
    }
}
