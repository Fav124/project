<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\HealthManagementValidation;
use App\Http\Controllers\Concerns\SendsGuardianWhatsApp;

use App\Models\Medicine;
use App\Models\Santri;
use App\Models\SicknessCase;
use App\Services\MedicineStockService;
use App\Services\WhatsAppService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class SicknessCaseController extends Controller
{
    use HealthManagementValidation, SendsGuardianWhatsApp;
    private const ACTIVE_STATUSES = ['observed', 'handled', 'referred'];

    public function index(Request $request)
    {
        $query = SicknessCase::with(['santri', 'handler', 'medicines']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('santri', function ($builder) use ($search) {
                $builder->where('name', 'like', '%' . $search . '%')
                    ->orWhere('nis', 'like', '%' . $search . '%');
            });
        }

        $cases = $query->latest('visit_date')->latest()->paginate(10)->withQueryString();
        $santris = Santri::orderBy('name')->get();
        $medicines = Medicine::orderBy('name')->get();

        $editCase = $request->filled('edit')
            ? SicknessCase::find($request->edit)
            : null;
        $detailCase = $request->filled('detail')
            ? SicknessCase::with(['santri', 'handler', 'medicines'])->find($request->detail)
            : null;
        $showForm = $request->boolean('create') || $editCase || $request->isMethod('post');

        // Chart Data
        $sicknessTrends = SicknessCase::select(\DB::raw('DATE(visit_date) as date'), \DB::raw('COUNT(*) as count'))
            ->where('visit_date', '>=', now()->subDays(30))
            ->groupBy('date')
            ->orderBy('date')
            ->get();
            
        $diagnosisStats = SicknessCase::select('diagnosis', \DB::raw('COUNT(*) as count'))
            ->whereNotNull('diagnosis')
            ->groupBy('diagnosis')
            ->orderBy('count', 'desc')
            ->take(5)
            ->get();
            
        $statusStats = SicknessCase::select('status', \DB::raw('COUNT(*) as count'))
            ->groupBy('status')
            ->get();

        return view('health.sickness-cases.index', compact(
            'cases', 'santris', 'medicines', 'editCase', 'detailCase', 'showForm',
            'sicknessTrends', 'diagnosisStats', 'statusStats'
        ));
    }

    public function show(SicknessCase $sicknessCase)
    {
        $sicknessCase->load(['santri', 'handledBy', 'medicines']);
        $medicines = Medicine::orderBy('name')->get();
        return view('health.sickness-cases.show', compact('sicknessCase', 'medicines'));
    }

    public function store(Request $request, WhatsAppService $whatsApp)
    {
        $validated = $request->validate([
            'cases' => ['required', 'array', 'min:1'],
            'cases.*.santri_id' => ['required', 'exists:santris,id'],
            'cases.*.medicines' => ['nullable', 'array'],
            'cases.*.medicines.*.id' => ['nullable', 'exists:medicines,id'],
            'cases.*.medicines.*.quantity' => ['nullable', 'integer', 'min:1'],

            'cases.*.visit_date' => ['required', 'date'],
            'cases.*.complaint' => ['required', 'string'],
            'cases.*.diagnosis' => ['nullable', 'string', 'max:255'],
            'cases.*.status' => ['required', 'in:observed,handled,recovered,referred'],
        ]);

        $this->validateBatchCaseRelations($validated['cases']);

        DB::transaction(function () use ($validated): void {
            foreach ($validated['cases'] as $caseData) {
                $this->ensureCaseRelations(
                    $caseData['santri_id'],
                    $caseData['status']
                );

                $caseData['handled_by'] = auth()->id();
                $medicines = $caseData['medicines'] ?? [];
                unset($caseData['medicines']);
                
                $case = SicknessCase::create($caseData);
                
                if (!empty($medicines)) {
                    $attachData = [];
                    foreach ($medicines as $med) {
                        if (!empty($med['id'])) {
                            $attachData[$med['id']] = [
                                'quantity' => $med['quantity'] ?? 1,
                                'status' => 'pending'
                            ];
                        }
                    }
                    $case->medicines()->attach($attachData);
                }
                

            }
        });

        $message = count($validated['cases']) . ' data santri sakit berhasil ditambahkan.';
        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => $message]);
        }

        return redirect()->route('sickness-cases.index')->with('success', $message);
    }

    public function update(Request $request, SicknessCase $sicknessCase, WhatsAppService $whatsApp)
    {
        $validated = $request->validate([
            'santri_id' => ['required', 'exists:santris,id'],
            'visit_date' => ['required', 'date'],
            'complaint' => ['required', 'string'],
            'diagnosis' => ['nullable', 'string', 'max:255'],
            'status' => ['required', 'in:observed,handled,recovered,referred'],
            'notes' => ['nullable', 'string'],
            'medicines' => ['nullable', 'array'],
            'medicines.*.id' => ['required_with:medicines', 'exists:medicines,id'],
            'medicines.*.quantity' => ['required_with:medicines', 'integer', 'min:1'],
        ]);

        $validated['handled_by'] = auth()->id();
        $medicines = $validated['medicines'] ?? [];
        unset($validated['medicines']);

        $this->ensureCaseRelations(
            $validated['santri_id'],
            $validated['status'],
            $sicknessCase->id
        );

        DB::transaction(function () use ($sicknessCase, $validated, $medicines): void {
            $sicknessCase->update($validated);

            $syncData = [];
            foreach ($medicines as $med) {
                $syncData[$med['id']] = ['quantity' => $med['quantity']];
            }
            $sicknessCase->medicines()->sync($syncData);
        });

        $successMessage = 'Data santri sakit berhasil diperbarui.';
        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => $successMessage]);
        }

        return redirect()->route('sickness-cases.index')->with('success', $successMessage);
    }

    public function notifyGuardian(SicknessCase $sicknessCase, WhatsAppService $whatsApp)
    {
        $result = $this->sendSicknessCaseNotification($sicknessCase, $whatsApp);

        return back()->with($result['success'] ? 'success' : 'warning', $result['message']);
    }

    public function destroy(SicknessCase $sicknessCase)
    {
        $sicknessCase->delete();

        return redirect()->route('sickness-cases.index')
            ->with('success', 'Data santri sakit berhasil dihapus.');
    }

    public function markRecovered(SicknessCase $sicknessCase)
    {
        $sicknessCase->update([
            'status' => 'recovered',
            'return_date' => now()
        ]);

        return back()->with('success', 'Santri telah dinyatakan sembuh.');
    }

    public function edit(SicknessCase $sicknessCase)
    {
        $sicknessCase->load(['santri', 'handledBy', 'medicines']);
        $santris = Santri::orderBy('name')->get();
        $medicines = Medicine::orderBy('name')->get();
        
        return view('health.sickness-cases.edit', compact('sicknessCase', 'santris', 'medicines'));
    }

    public function updateMedicineStatus(Request $request, $pivotId, MedicineStockService $stockService)
    {
        $request->validate(['status' => 'required|in:pending,taken']);

        $pivot = DB::table('medicine_sickness_case')->where('id', $pivotId)->first();
        if (!$pivot) {
            return response()->json(['success' => false, 'message' => 'Data obat tidak ditemukan.'], 404);
        }

        $oldStatus = $pivot->status;
        $newStatus = $request->status;

        if ($oldStatus === $newStatus) {
            return response()->json(['success' => true, 'message' => 'Status pemakaian obat diperbarui.']);
        }

        try {
            DB::transaction(function () use ($pivot, $oldStatus, $newStatus, $stockService) {
                if ($newStatus === 'taken' && $oldStatus === 'pending') {
                    $stockService->dispense(
                        $pivot->medicine_id,
                        $pivot->quantity,
                        'Obat diberikan - kasus #' . $pivot->sickness_case_id
                    );
                } elseif ($newStatus === 'pending' && $oldStatus === 'taken') {
                    $stockService->restoreDispense(
                        $pivot->medicine_id,
                        $pivot->quantity,
                        'Pembatalan obat - kasus #' . $pivot->sickness_case_id
                    );
                }

                DB::table('medicine_sickness_case')
                    ->where('id', $pivot->id)
                    ->update(['status' => $newStatus, 'updated_at' => now()]);
            });
        } catch (\InvalidArgumentException $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }

        return response()->json(['success' => true, 'message' => 'Status pemakaian obat diperbarui.']);
    }

    public function addMedicineToCase(Request $request, $sicknessCaseId, MedicineStockService $stockService)
    {
        $validated = $request->validate([
            'medicine_id' => 'required|exists:medicines,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $case = SicknessCase::findOrFail($sicknessCaseId);
        $medicine = Medicine::findOrFail($validated['medicine_id']);

        // Check if medicine already exists in this case
        $existingPivot = DB::table('medicine_sickness_case')
            ->where('sickness_case_id', $sicknessCaseId)
            ->where('medicine_id', $validated['medicine_id'])
            ->first();

        if ($existingPivot) {
            return response()->json(['success' => false, 'message' => 'Obat ini sudah ada dalam kasus ini.'], 422);
        }

        try {
            DB::transaction(function () use ($case, $medicine, $validated, $stockService) {
                // Attach medicine to case
                DB::table('medicine_sickness_case')->insert([
                    'sickness_case_id' => $case->id,
                    'medicine_id' => $medicine->id,
                    'quantity' => $validated['quantity'],
                    'status' => 'pending',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            });
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Gagal menambahkan obat: ' . $e->getMessage()], 500);
        }

        return response()->json(['success' => true, 'message' => 'Obat berhasil ditambahkan ke kasus.']);
    }



    private function validateBatchCaseRelations(array $cases): void
    {
        $seenSantri = [];

        foreach ($cases as $index => $caseData) {
            $santriId = $caseData['santri_id'] ?? null;
            $isActive = in_array($caseData['status'] ?? null, self::ACTIVE_STATUSES, true);

            if (!$isActive) {
                continue;
            }

            if ($santriId && isset($seenSantri[$santriId])) {
                throw ValidationException::withMessages([
                    "cases.$index.santri_id" => 'Santri tidak boleh dicatat dua kali dalam kasus aktif pada input yang sama.',
                ]);
            }



            if ($santriId) {
                $seenSantri[$santriId] = true;
            }
        }
    }

    private function ensureCaseRelations(int $santriId, string $status, ?int $ignoreCaseId = null): void
    {
        $isActive = in_array($status, self::ACTIVE_STATUSES, true);
        if (!$isActive) {
            return;
        }

        $santriQuery = SicknessCase::where('santri_id', $santriId)
            ->whereIn('status', self::ACTIVE_STATUSES);

        if ($ignoreCaseId) {
            $santriQuery->whereKeyNot($ignoreCaseId);
        }

        if ($santriQuery->exists()) {
            throw ValidationException::withMessages([
                'santri_id' => 'Santri masih memiliki kasus aktif. Selesaikan kasus sebelumnya terlebih dahulu.',
            ]);
        }
    }
}
