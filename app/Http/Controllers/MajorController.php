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

        return view('health.majors.index', compact('majors'));
    }

    public function show(Major $major)
    {
        $major->load('santris');
        return view('health.majors.show', compact('major'));
    }

    public function edit(Major $major)
    {
        return view('health.majors.edit', compact('major'));
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
