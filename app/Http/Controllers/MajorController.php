<?php

namespace App\Http\Controllers;

use App\Models\Major;
use Illuminate\Http\Request;

class MajorController extends Controller
{
    public function index(Request $request)
    {
        $query = Major::query();

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $majors = $query->latest()->paginate(10)->withQueryString();
        $editMajor = $request->filled('edit') ? Major::find($request->edit) : null;
        $showForm = $request->boolean('create') || $editMajor || $request->isMethod('post');

        return view('health.majors.index', compact('majors', 'editMajor', 'showForm'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:majors,name'],
            'description' => ['nullable', 'string'],
        ]);

        Major::create($validated);

        return redirect()->route('majors.index')
            ->with('success', 'Data jurusan berhasil ditambahkan.');
    }

    public function update(Request $request, Major $major)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:majors,name,' . $major->id],
            'description' => ['nullable', 'string'],
        ]);

        $major->update($validated);

        return redirect()->route('majors.index')
            ->with('success', 'Data jurusan berhasil diperbarui.');
    }

    public function destroy(Major $major)
    {
        $major->delete();

        return redirect()->route('majors.index')
            ->with('success', 'Data jurusan berhasil dihapus.');
    }
}
