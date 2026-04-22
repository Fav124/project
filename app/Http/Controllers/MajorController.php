<?php

namespace App\Http\Controllers;

use App\Models\Major;
use Illuminate\Http\Request;

class MajorController extends Controller
{
    public function index(Request $request)
    {
        $query = Major::withCount('santris');

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $majors = $query->latest()->paginate(10)->withQueryString();
        $editMajor = $request->filled('edit') ? Major::find($request->edit) : null;
        $detailMajor = $request->filled('detail') ? Major::with('santris')->find($request->detail) : null;
        $showForm = $request->boolean('create') || $editMajor || $request->isMethod('post');

        return view('health.majors.index', compact('majors', 'editMajor', 'detailMajor', 'showForm'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'majors' => ['required', 'array', 'min:1'],
            'majors.*.name' => ['required', 'string', 'max:255', 'unique:majors,name'],
            'majors.*.description' => ['nullable', 'string'],
        ]);

        foreach ($validated['majors'] as $majorData) {
            Major::create($majorData);
        }

        $message = count($validated['majors']) . ' data jurusan berhasil ditambahkan.';
        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => $message]);
        }

        return redirect()->route('majors.index')->with('success', $message);
    }

    public function update(Request $request, Major $major)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:majors,name,' . $major->id],
            'description' => ['nullable', 'string'],
        ]);

        $major->update($validated);

        $message = 'Data jurusan berhasil diperbarui.';
        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => $message]);
        }

        return redirect()->route('majors.index')->with('success', $message);
    }

    public function destroy(Major $major)
    {
        $major->delete();

        return redirect()->route('majors.index')
            ->with('success', 'Data jurusan berhasil dihapus.');
    }
}
