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

class SicknessCaseController extends Controller
{
    use HealthManagementValidation, SendsGuardianWhatsApp;

    public function index(Request $request)
    {
        $query = SicknessCase::with(['santri', 'handler', 'medicine', 'bed']);

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
        $showForm = $request->boolean('create') || $editCase || $request->isMethod('post');

        return view('health.sickness-cases.index', compact('cases', 'santris', 'medicines', 'beds', 'editCase', 'showForm'));
    }

    public function show(SicknessCase $sicknessCase)
    {
        $sicknessCase->load(['santri', 'handledBy', 'medicine', 'bed']);
        return view('health.sickness-cases.show', compact('sicknessCase'));
    }

    public function store(Request $request, WhatsAppService $whatsApp)
    {
        $validated = $request->validate($this->sicknessCaseRules());
        $validated['handled_by'] = auth()->id();

        $case = SicknessCase::create($validated);
        $this->syncBedStatus($case);

        $successMessage = 'Data santri sakit berhasil ditambahkan.';
        $redirect = redirect()->route('sickness-cases.index')
            ->with('success', $successMessage);

        if ($request->boolean('notify_guardian')) {
            $result = $this->sendSicknessCaseNotification($case, $whatsApp);
            if ($result['success']) {
                $redirect->with('success', $successMessage . ' ' . $result['message']);
            } else {
                $redirect->with('warning', $result['message']);
            }
        }

        return $redirect;
    }

    public function update(Request $request, SicknessCase $sicknessCase, WhatsAppService $whatsApp)
    {
        $validated = $request->validate($this->sicknessCaseRules());
        $validated['handled_by'] = auth()->id();

        $oldBedId = $sicknessCase->infirmary_bed_id;
        $sicknessCase->update($validated);

        if ($oldBedId && $oldBedId !== $sicknessCase->infirmary_bed_id) {
            InfirmaryBed::whereKey($oldBedId)->update([
                'status' => 'available',
                'occupant_name' => null,
            ]);
        }

        $this->syncBedStatus($sicknessCase);

        $successMessage = 'Data santri sakit berhasil diperbarui.';
        $redirect = redirect()->route('sickness-cases.index')
            ->with('success', $successMessage);

        if ($request->boolean('notify_guardian')) {
            $result = $this->sendSicknessCaseNotification($sicknessCase->fresh(), $whatsApp);
            if ($result['success']) {
                $redirect->with('success', $successMessage . ' ' . $result['message']);
            } else {
                $redirect->with('warning', $result['message']);
            }
        }

        return $redirect;
    }

    public function notifyGuardian(SicknessCase $sicknessCase, WhatsAppService $whatsApp)
    {
        $result = $this->sendSicknessCaseNotification($sicknessCase, $whatsApp);

        return back()->with($result['success'] ? 'success' : 'warning', $result['message']);
    }

    public function destroy(SicknessCase $sicknessCase)
    {
        if ($sicknessCase->infirmary_bed_id) {
            InfirmaryBed::whereKey($sicknessCase->infirmary_bed_id)->update([
                'status' => 'available',
                'occupant_name' => null,
            ]);
        }

        $sicknessCase->delete();

        return redirect()->route('sickness-cases.index')
            ->with('success', 'Data santri sakit berhasil dihapus.');
    }

    private function syncBedStatus(SicknessCase $case): void
    {
        if (!$case->infirmary_bed_id) {
            return;
        }

        $bedStatus = in_array($case->status, ['recovered']) ? 'available' : 'occupied';
        $occupant = $bedStatus === 'occupied' ? $case->santri->name : null;

        InfirmaryBed::whereKey($case->infirmary_bed_id)->update([
            'status' => $bedStatus,
            'occupant_name' => $occupant,
        ]);
    }
}
