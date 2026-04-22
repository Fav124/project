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
        $detailRecord = $request->filled('detail')
            ? HealthRecord::with(['santri', 'recorder'])->find($request->detail)
            : null;
        $showForm = $request->boolean('create') || $editRecord || $request->isMethod('post');

        return view('health.records.index', compact('records', 'santris', 'editRecord', 'detailRecord', 'showForm'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'records' => ['required', 'array', 'min:1'],
            'records.*.santri_id' => ['required', 'exists:santris,id'],
            'records.*.record_date' => ['required', 'date'],
            'records.*.complaint' => ['required', 'string'],
            'records.*.diagnosis' => ['nullable', 'string', 'max:255'],
            'records.*.treatment' => ['nullable', 'string'],
            'records.*.blood_pressure' => ['nullable', 'string', 'max:50'],
            'records.*.temperature' => ['nullable', 'numeric', 'between:30,45'],
        ]);

        foreach ($validated['records'] as $recordData) {
            $recordData['recorded_by'] = auth()->id();
            HealthRecord::create($recordData);
        }

        $message = count($validated['records']) . ' rekam kesehatan berhasil ditambahkan.';
        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => $message]);
        }

        return redirect()->route('health-records.index')->with('success', $message);
    }

    public function update(Request $request, HealthRecord $healthRecord)
    {
        $validated = $request->validate($this->healthRecordRules());
        $validated['recorded_by'] = auth()->id();

        $healthRecord->update($validated);

        $message = 'Rekam kesehatan berhasil diperbarui.';
        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => $message]);
        }

        return redirect()->route('health-records.index')->with('success', $message);
    }

    public function destroy(HealthRecord $healthRecord)
    {
        $healthRecord->delete();

        return redirect()->route('health-records.index')
            ->with('success', 'Rekam kesehatan berhasil dihapus.');
    }
}
