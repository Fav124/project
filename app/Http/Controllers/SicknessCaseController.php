<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\HealthManagementValidation;
use App\Http\Controllers\Concerns\SendsGuardianWhatsApp;
use App\Models\InfirmaryBed;
use App\Models\Medicine;
use App\Models\Santri;
use App\Models\SicknessCase;
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
        $query = SicknessCase::with(['santri', 'handler', 'medicines', 'bed']);

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
        $beds = InfirmaryBed::whereIn('status', ['available', 'occupied'])->orderBy('code')->get();
        $editCase = $request->filled('edit')
            ? SicknessCase::find($request->edit)
            : null;
        $detailCase = $request->filled('detail')
            ? SicknessCase::with(['santri', 'handler', 'medicines', 'bed'])->find($request->detail)
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
            'cases', 'santris', 'medicines', 'beds', 'editCase', 'detailCase', 'showForm',
            'sicknessTrends', 'diagnosisStats', 'statusStats'
        ));
    }

    public function show(SicknessCase $sicknessCase)
    {
        $sicknessCase->load(['santri', 'handledBy', 'medicine', 'bed']);
        return view('health.sickness-cases.show', compact('sicknessCase'));
    }

    public function store(Request $request, WhatsAppService $whatsApp)
    {
        $validated = $request->validate([
            'cases' => ['required', 'array', 'min:1'],
            'cases.*.santri_id' => ['required', 'exists:santris,id'],
            'cases.*.medicine_id' => ['nullable', 'exists:medicines,id'],
            'cases.*.infirmary_bed_id' => ['nullable', 'exists:infirmary_beds,id'],
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
                    $caseData['infirmary_bed_id'] ?? null,
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
                
                $this->syncBedStatus($case);
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
            'infirmary_bed_id' => ['nullable', 'exists:infirmary_beds,id'],
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
            $validated['infirmary_bed_id'] ?? null,
            $validated['status'],
            $sicknessCase->id
        );

        $oldBedId = $sicknessCase->infirmary_bed_id;
        DB::transaction(function () use ($sicknessCase, $validated, $medicines, $oldBedId): void {
            $sicknessCase->update($validated);

            $syncData = [];
            foreach ($medicines as $med) {
                $syncData[$med['id']] = ['quantity' => $med['quantity']];
            }
            $sicknessCase->medicines()->sync($syncData);

            if ($oldBedId && $oldBedId !== $sicknessCase->infirmary_bed_id) {
                $hasAnotherActiveOccupant = SicknessCase::where('infirmary_bed_id', $oldBedId)
                    ->whereIn('status', self::ACTIVE_STATUSES)
                    ->whereKeyNot($sicknessCase->id)
                    ->exists();

                if (!$hasAnotherActiveOccupant) {
                    InfirmaryBed::whereKey($oldBedId)->update([
                        'status' => 'available',
                        'occupant_name' => null,
                    ]);
                }
            }

            $this->syncBedStatus($sicknessCase->fresh('santri'));
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
        if ($sicknessCase->infirmary_bed_id) {
            $hasAnotherActiveOccupant = SicknessCase::where('infirmary_bed_id', $sicknessCase->infirmary_bed_id)
                ->whereIn('status', self::ACTIVE_STATUSES)
                ->whereKeyNot($sicknessCase->id)
                ->exists();

            if (!$hasAnotherActiveOccupant) {
                InfirmaryBed::whereKey($sicknessCase->infirmary_bed_id)->update([
                    'status' => 'available',
                    'occupant_name' => null,
                ]);
            }
        }

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

        $this->syncBedStatus($sicknessCase);

        return back()->with('success', 'Santri telah dinyatakan sembuh dan status kasur telah diperbarui.');
    }

    public function updateMedicineStatus(Request $request, $pivotId)
    {
        $request->validate(['status' => 'required|in:pending,taken']);
        
        \DB::table('medicine_sickness_case')
            ->where('id', $pivotId)
            ->update(['status' => $request->status]);

        return response()->json(['success' => true, 'message' => 'Status pemakaian obat diperbarui.']);
    }

    private function syncBedStatus(SicknessCase $case): void
    {
        if (!$case->infirmary_bed_id) {
            return;
        }

        $bedStatus = $case->status === 'recovered' ? 'available' : 'occupied';
        $occupant = $bedStatus === 'occupied' ? $case->santri->name : null;

        InfirmaryBed::whereKey($case->infirmary_bed_id)->update([
            'status' => $bedStatus,
            'occupant_name' => $occupant,
        ]);
    }

    private function validateBatchCaseRelations(array $cases): void
    {
        $seenSantri = [];
        $seenBeds = [];

        foreach ($cases as $index => $caseData) {
            $santriId = $caseData['santri_id'] ?? null;
            $bedId = $caseData['infirmary_bed_id'] ?? null;
            $isActive = in_array($caseData['status'] ?? null, self::ACTIVE_STATUSES, true);

            if (!$isActive) {
                continue;
            }

            if ($santriId && isset($seenSantri[$santriId])) {
                throw ValidationException::withMessages([
                    "cases.$index.santri_id" => 'Santri tidak boleh dicatat dua kali dalam kasus aktif pada input yang sama.',
                ]);
            }

            if ($bedId && isset($seenBeds[$bedId])) {
                throw ValidationException::withMessages([
                    "cases.$index.infirmary_bed_id" => 'Kasur UKS tidak boleh dipakai lebih dari satu kasus aktif.',
                ]);
            }

            if ($santriId) {
                $seenSantri[$santriId] = true;
            }
            if ($bedId) {
                $seenBeds[$bedId] = true;
            }
        }
    }

    private function ensureCaseRelations(int $santriId, ?int $bedId, string $status, ?int $ignoreCaseId = null): void
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

        if ($bedId) {
            $bedQuery = SicknessCase::where('infirmary_bed_id', $bedId)
                ->whereIn('status', self::ACTIVE_STATUSES);

            if ($ignoreCaseId) {
                $bedQuery->whereKeyNot($ignoreCaseId);
            }

            if ($bedQuery->exists()) {
                throw ValidationException::withMessages([
                    'infirmary_bed_id' => 'Kasur UKS ini sedang dipakai kasus aktif lain.',
                ]);
            }
        }
    }
}
