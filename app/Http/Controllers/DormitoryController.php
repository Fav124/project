<?php

namespace App\Http\Controllers;

use App\Models\Dormitory;
use Illuminate\Http\Request;

class DormitoryController extends Controller
{
    public function index(Request $request)
    {
        $query = Dormitory::withCount('santris');

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('building', 'like', '%' . $request->search . '%');
        }

        $dormitories = $query->orderBy('name')->paginate(10)->withQueryString();
        $editDormitory = $request->filled('edit') ? Dormitory::find($request->edit) : null;

        // Chart Data
        $dormitoryStats = Dormitory::withCount('santris')->get();
        $genderStats = Dormitory::select('gender', \DB::raw('count(*) as count'))->groupBy('gender')->get();

        return view('health.dormitories.index', compact('dormitories', 'editDormitory', 'dormitoryStats', 'genderStats'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'dormitories' => ['required', 'array', 'min:1'],
            'dormitories.*.name' => ['required', 'string', 'max:255'],
            'dormitories.*.building' => ['nullable', 'string', 'max:255'],
            'dormitories.*.gender' => ['required', 'in:L,P'],
            'dormitories.*.supervisor_name' => ['nullable', 'string', 'max:255'],
            'dormitories.*.description' => ['nullable', 'string'],
        ]);

        foreach ($validated['dormitories'] as $dormitoryData) {
            Dormitory::create($dormitoryData);
        }

        $message = count($validated['dormitories']) . ' data asrama berhasil ditambahkan.';
        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => $message]);
        }

        return redirect()->route('dormitories.index')->with('success', $message);
    }

    public function update(Request $request, Dormitory $dormitory)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'building' => ['nullable', 'string', 'max:255'],
            'gender' => ['required', 'in:L,P'],
            'supervisor_name' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
        ]);

        $dormitory->update($validated);

        $message = 'Data asrama berhasil diperbarui.';
        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => $message]);
        }

        return redirect()->route('dormitories.index')->with('success', $message);
    }

    public function destroy(Dormitory $dormitory)
    {
        $dormitory->delete();

        return redirect()->route('dormitories.index')
            ->with('success', 'Data asrama berhasil dihapus.');
    }
}
