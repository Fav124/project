<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\HealthManagementValidation;
use App\Models\HealthRecord;
use App\Models\Santri;
use Illuminate\Http\Request;

class HealthRecordController extends Controller
{
    use HealthManagementValidation;

    public function index(Request $request)
    {
        $query = HealthRecord::with(['santri', 'recorder']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('santri', function ($builder) use ($search) {
                $builder->where('name', 'like', '%' . $search . '%')
                    ->orWhere('nis', 'like', '%' . $search . '%');
            });
        }

        if ($request->filled('date_from')) {
            $query->whereDate('record_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('record_date', '<=', $request->date_to);
        }

        $records = $query->latest('record_date')->latest()->paginate(10)->withQueryString();
        $santris = Santri::orderBy('name')->get();
        $editRecord = $request->filled('edit')
            ? HealthRecord::find($request->edit)
            : null;
        $showForm = $request->boolean('create') || $editRecord || $request->isMethod('post');

        return view('health.records.index', compact('records', 'santris', 'editRecord', 'showForm'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate($this->healthRecordRules());
        $validated['recorded_by'] = auth()->id();

        HealthRecord::create($validated);

        return redirect()->route('health-records.index')
            ->with('success', 'Rekam kesehatan berhasil ditambahkan.');
    }

    public function update(Request $request, HealthRecord $healthRecord)
    {
        $validated = $request->validate($this->healthRecordRules());
        $validated['recorded_by'] = auth()->id();

        $healthRecord->update($validated);

        return redirect()->route('health-records.index')
            ->with('success', 'Rekam kesehatan berhasil diperbarui.');
    }

    public function destroy(HealthRecord $healthRecord)
    {
        $healthRecord->delete();

        return redirect()->route('health-records.index')
            ->with('success', 'Rekam kesehatan berhasil dihapus.');
    }
}
