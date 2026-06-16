<?php

namespace App\Http\Controllers\Api;

use App\Models\Diagnosa;
use App\Models\Keluhan;
use App\Models\Medicine;
use App\Models\Santri;
use App\Models\SicknessCase;
use App\Models\Tindakan;
use App\Services\WhatsAppService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class SicknessCaseApiController extends BaseApiController
{
    // ─── Lookups ──────────────────────────────────────────────────────────────

    public function lookups()
    {
        return $this->success([
            'santris'   => Santri::with('guardians')->orderBy('name')->get()->map(fn($s) => [
                'id'       => $s->id,
                'name'     => $s->name,
                'nis'      => $s->nis,
                'gender'   => $s->gender,
                'guardians' => $s->guardians->map(fn($g) => [
                    'id'    => $g->id, 'name' => $g->name, 'phone' => $g->phone,
                    'relationship' => $g->relationship, 'is_primary' => (bool) $g->is_primary,
                ])->values(),
            ])->values(),
                'id' => $b->id, 'code' => $b->code, 'room' => $b->room_name,
            ])->values(),
            'medicines' => Medicine::orderBy('name')->get(['id', 'name', 'unit', 'stock'])->map(fn($m) => [
                'id' => $m->id, 'name' => $m->name, 'unit' => $m->unit, 'stock' => $m->stock,
            ])->values(),
            'diagnoses' => Diagnosa::orderBy('name')->get(['id', 'name']),
            'keluhans'  => Keluhan::orderBy('name')->get(['id', 'name']),
            'tindakans' => Tindakan::orderBy('name')->get(['id', 'name']),
        ]);
    }

    // ─── CRUD ─────────────────────────────────────────────────────────────────

    public function index(Request $request)
    {
        $query = SicknessCase::with(['santri:id,name,nis,gender', 'medicines:id,name', 'bed:id,code,room_name']);

        if ($request->filled('status'))     $query->where('status', $request->status);
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('santri', fn($b) => $b->where('name', 'like', "%$search%")->orWhere('nis', 'like', "%$search%"));
        }
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('visit_date', [$request->start_date, $request->end_date]);
        }

        $cases = $query->latest('visit_date')->paginate((int) $request->input('per_page', 15));

        return response()->json([
            'success' => true,
            'data'    => $cases->map(fn($c) => $this->formatCase($c)),
            'meta'    => [
                'current_page' => $cases->currentPage(),
                'last_page'    => $cases->lastPage(),
                'total'        => $cases->total(),
            ],
        ]);
    }

    public function show($id)
    {
        $case = SicknessCase::with([
            'santri.dormitory', 'santri.schoolClass', 'santri.major', 'santri.guardians',
            'medicines', 'bed', 'handledBy:id,name',
            'keluhans', 'diagnosas', 'tindakans',
        ])->findOrFail($id);

        return $this->success($this->formatCaseDetail($case));
    }

    public function store(Request $request, WhatsAppService $whatsApp)
    {
        $validated = $request->validate([
            'santri_id'        => 'required|exists:santris,id',
            'santri_ids'       => 'nullable|array',
            'santri_ids.*'     => 'exists:santris,id',
            'visit_date'       => 'required|date',
            'complaint'        => 'nullable|string',
            'diagnosis'        => 'nullable|string|max:255',
            'action_taken'     => 'nullable|string',
            'notes'            => 'nullable|string',
            'status'           => 'required|in:observed,handled,recovered,referred',
            'medicines'        => 'nullable|array',
            'medicines.*.id'   => 'required_with:medicines|exists:medicines,id',
            'medicines.*.quantity' => 'required_with:medicines|integer|min:1',
            'keluhan_ids'      => 'nullable|array',
            'keluhan_ids.*'    => 'exists:keluhans,id',
            'diagnosa_ids'     => 'nullable|array',
            'diagnosa_ids.*'   => 'exists:diagnosas,id',
            'tindakan_ids'     => 'nullable|array',
            'tindakan_ids.*'   => 'exists:tindakans,id',
            'photo_base64'     => 'nullable|string',
            'notify_guardian'  => 'nullable|boolean',
            // Referral fields
            'hospital_name'    => 'nullable|string|max:255',
            'transport'        => 'nullable|string|max:100',
            'companion_name'   => 'nullable|string|max:100',
        ]);

        $validated['handled_by'] = auth()->id();
        $medicinesData   = $validated['medicines'] ?? [];
        $keluhanIds      = $validated['keluhan_ids'] ?? [];
        $diagnosaIds     = $validated['diagnosa_ids'] ?? [];
        $tindakanIds     = $validated['tindakan_ids'] ?? [];
        $notifyGuardian  = $validated['notify_guardian'] ?? false;
        $photoBase64     = $validated['photo_base64'] ?? null;

        // Handle photo upload
        $photoPath = null;
        if ($photoBase64) {
            $photoPath = $this->saveBase64Photo($photoBase64);
            $validated['photo_path'] = $photoPath;
        }

        unset($validated['medicines'], $validated['notify_guardian'], $validated['keluhan_ids'],
              $validated['diagnosa_ids'], $validated['tindakan_ids'], $validated['photo_base64'],
              $validated['santri_ids']);

        $case = SicknessCase::create($validated);

        // Medicines
        if (!empty($medicinesData)) {
            $attachData = [];
            foreach ($medicinesData as $med) {
                $attachData[$med['id']] = ['quantity' => $med['quantity'] ?? 1, 'status' => 'pending'];
            }
            $case->medicines()->attach($attachData);
        }

        // Lookups
        if (!empty($keluhanIds))  $case->keluhans()->sync($keluhanIds);
        if (!empty($diagnosaIds)) $case->diagnosas()->sync($diagnosaIds);
        if (!empty($tindakanIds)) $case->tindakans()->sync($tindakanIds);

        $this->syncBedStatus($case);

        if ($notifyGuardian) {
            $this->sendSicknessNotification($case->load('santri'), $whatsApp);
        }

        return $this->success(
            $this->formatCaseDetail($case->load(['santri', 'medicines', 'bed', 'handledBy', 'keluhans', 'diagnosas', 'tindakans'])),
            'Data kasus sakit berhasil disimpan.',
            201
        );
    }

    public function update(Request $request, $id, WhatsAppService $whatsApp)
    {
        $case = SicknessCase::findOrFail($id);

        $validated = $request->validate([
            'santri_id'        => 'required|exists:santris,id',
            'visit_date'       => 'required|date',
            'complaint'        => 'nullable|string',
            'diagnosis'        => 'nullable|string|max:255',
            'action_taken'     => 'nullable|string',
            'notes'            => 'nullable|string',
            'status'           => 'required|in:observed,handled,recovered,referred',
            'medicines'        => 'nullable|array',
            'medicines.*.id'   => 'required_with:medicines|exists:medicines,id',
            'medicines.*.quantity' => 'required_with:medicines|integer|min:1',
            'keluhan_ids'      => 'nullable|array',
            'keluhan_ids.*'    => 'exists:keluhans,id',
            'diagnosa_ids'     => 'nullable|array',
            'diagnosa_ids.*'   => 'exists:diagnosas,id',
            'tindakan_ids'     => 'nullable|array',
            'tindakan_ids.*'   => 'exists:tindakans,id',
            'hospital_name'    => 'nullable|string|max:255',
            'transport'        => 'nullable|string|max:100',
            'companion_name'   => 'nullable|string|max:100',
        ]);

        $medicinesData = $validated['medicines'] ?? [];
        $keluhanIds    = $validated['keluhan_ids'] ?? null;
        $diagnosaIds   = $validated['diagnosa_ids'] ?? null;
        $tindakanIds   = $validated['tindakan_ids'] ?? null;

        unset($validated['medicines'], $validated['keluhan_ids'], $validated['diagnosa_ids'], $validated['tindakan_ids']);

        $validated['handled_by'] = auth()->id();
        $case->update($validated);

        $syncData = [];
        foreach ($medicinesData as $med) {
            $syncData[$med['id']] = ['quantity' => $med['quantity']];
        }
        $case->medicines()->sync($syncData);

        if ($keluhanIds !== null)  $case->keluhans()->sync($keluhanIds);
        if ($diagnosaIds !== null) $case->diagnosas()->sync($diagnosaIds);
        if ($tindakanIds !== null) $case->tindakans()->sync($tindakanIds);

        }
        $this->syncBedStatus($case);

        return $this->success(
            $this->formatCaseDetail($case->load(['santri', 'medicines', 'bed', 'handledBy', 'keluhans', 'diagnosas', 'tindakans'])),
            'Data berhasil diperbarui.'
        );
    }

    public function destroy($id)
    {
        $case = SicknessCase::findOrFail($id);

        }

        $case->delete();

        return $this->success([], 'Data berhasil dihapus.');
    }

    // ─── Special Actions ──────────────────────────────────────────────────────

    public function markRecovered($id)
    {
        $case = SicknessCase::findOrFail($id);
        $case->update(['status' => 'recovered', 'return_date' => now()]);
        $this->syncBedStatus($case);

        return $this->success(
            ['status' => 'recovered', 'status_label' => 'Sembuh'],
            'Santri dinyatakan sembuh.'
        );
    }

    public function discharge(Request $request, $id)
    {
        $case = SicknessCase::findOrFail($id);

        $validated = $request->validate([
            'picked_up_by'          => 'nullable|string|max:255',
            'discharge_guardian_id' => 'nullable|exists:guardians,id',
            'discharge_notes'       => 'nullable|string',
        ]);

        $case->update(array_merge($validated, [
            'status'      => 'recovered',
            'return_date' => now(),
        ]));

        $this->syncBedStatus($case);

        return $this->success(
            $this->formatCaseDetail($case->load(['santri', 'medicines', 'bed', 'handledBy'])),
            'Santri berhasil dipulangkan.'
        );
    }

    public function refer(Request $request, $id)
    {
        $case = SicknessCase::findOrFail($id);

        $validated = $request->validate([
            'hospital_name'  => 'required|string|max:255',
            'transport'      => 'nullable|string|max:100',
            'companion_name' => 'nullable|string|max:100',
            'notes'          => 'nullable|string',
        ]);

        $case->update(array_merge($validated, ['status' => 'referred']));

        $this->syncBedStatus($case);

        return $this->success(
            $this->formatCaseDetail($case->load(['santri', 'medicines', 'bed', 'handledBy'])),
            'Santri berhasil dirujuk.'
        );
    }

    public function assignBed(Request $request, $id)
    {
        $case = SicknessCase::findOrFail($id);

        $validated = $request->validate([
        ]);


        }

        $this->syncBedStatus($case);

        return $this->success(
            $this->formatCaseDetail($case->load(['santri', 'medicines', 'bed', 'handledBy'])),
            'Kasur berhasil ditetapkan.'
        );
    }

    public function notifyGuardian($id, WhatsAppService $whatsApp)
    {
        $case = SicknessCase::with('santri.guardians')->findOrFail($id);
        $this->sendSicknessNotification($case, $whatsApp);

        return $this->success([], 'Notifikasi berhasil dikirim.');
    }

    // ─── Helpers ──────────────────────────────────────────────────────────────

    private function syncBedStatus(SicknessCase $case): void
    {
        $isOccupied = !in_array($case->status, ['recovered']);
            'status'        => $isOccupied ? 'occupied' : 'available',
            'occupant_name' => $isOccupied ? $case->santri?->name : null,
        ]);
    }

    private function saveBase64Photo(string $base64): ?string
    {
        try {
            $data     = base64_decode($base64);
            $filename = 'photos/kunjungan/' . uniqid() . '.jpg';
            Storage::disk('public')->put($filename, $data);
            return $filename;
        } catch (\Throwable $e) {
            return null;
        }
    }

    private function sendSicknessNotification(SicknessCase $case, WhatsAppService $whatsApp): void
    {
        $santri = $case->santri;
        if (!$santri) return;

        // Try primary guardian or first guardian
        $primaryGuardian = $santri->guardians->firstWhere('is_primary', true) ?? $santri->guardians->first();
        $phone  = $primaryGuardian?->phone ?? $santri->guardian_phone;
        $name   = $primaryGuardian?->name  ?? $santri->guardian_name;

        if (!$phone) return;

        $message = "Assalamualaikum Bapak/Ibu *{$name}*,\n\n"
            . "Kami dari UKS Pondok Pesantren memberitahukan bahwa santri *{$santri->name}* sedang dalam penanganan kami.\n\n"
            . "📋 *Keluhan:* {$case->complaint}\n"
            . "🏥 *Status:* " . $this->translateStatus($case->status) . "\n"
            . "📅 *Tanggal:* " . $case->visit_date?->format('d M Y') . "\n\n"
            . "Jazakumullahu khairan. Semoga segera diberikan kesembuhan.";

        $whatsApp->sendTextMessage($phone, $message);
    }

    private function translateStatus(string $status): string
    {
        return match ($status) {
            'observed'  => 'Observasi',
            'handled'   => 'Ditangani',
            'recovered' => 'Sembuh',
            'referred'  => 'Dirujuk',
            default     => ucfirst($status),
        };
    }

    private function formatCase($case): array
    {
        return [
            'id'           => $case->id,
            'santri'       => $case->santri ? [
                'id' => $case->santri->id, 'name' => $case->santri->name, 'nis' => $case->santri->nis,
            ] : null,
            'complaint'    => $case->complaint,
            'diagnosis'    => $case->diagnosis,
            'status'       => $case->status,
            'status_label' => $this->translateStatus($case->status),
            'visit_date'   => $case->visit_date?->toDateString(),
            'photo_url'    => $case->photo_path ? asset('storage/' . $case->photo_path) : null,
            'medicines'    => $case->medicines->map(fn($m) => ['id' => $m->id, 'name' => $m->name])->values(),
        ];
    }

    private function formatCaseDetail($case): array
    {
        return [
            'id'             => $case->id,
            'santri'         => $case->santri ? [
                'id'            => $case->santri->id,
                'name'          => $case->santri->name,
                'nis'           => $case->santri->nis,
                'gender'        => $case->santri->gender,
                'dormitory'     => $case->santri->dormitory?->name,
                'class'         => $case->santri->schoolClass?->name,
                'guardian_name' => $case->santri->guardian_name,
                'guardian_phone'=> $case->santri->guardian_phone,
                'guardians'     => ($case->santri->guardians ?? collect())->map(fn($g) => [
                    'id' => $g->id, 'name' => $g->name, 'phone' => $g->phone,
                    'relationship' => $g->relationship, 'is_primary' => (bool) $g->is_primary,
                ])->values(),
            ] : null,
            'complaint'      => $case->complaint,
            'diagnosis'      => $case->diagnosis,
            'action_taken'   => $case->action_taken,
            'notes'          => $case->notes,
            'status'         => $case->status,
            'status_label'   => $this->translateStatus($case->status),
            'visit_date'     => $case->visit_date?->toDateString(),
            'return_date'    => $case->return_date?->toDateString(),
            'photo_url'      => $case->photo_path ? asset('storage/' . $case->photo_path) : null,
            'handled_by'     => $case->handledBy?->name,
            // Referral fields
            'hospital_name'  => $case->hospital_name ?? null,
            'transport'      => $case->transport ?? null,
            'companion_name' => $case->companion_name ?? null,
            // Discharge fields
            'picked_up_by'   => $case->picked_up_by ?? null,
            'picked_up_at'   => $case->picked_up_at ?? null,
            'discharge_notes'=> $case->discharge_notes ?? null,
            'bed'            => $case->bed ? [
                'id' => $case->bed->id, 'code' => $case->bed->code, 'room' => $case->bed->room_name,
            ] : null,
            'medicines'      => $case->medicines->map(fn($m) => [
                'id'       => $m->id,
                'name'     => $m->name,
                'unit'     => $m->unit,
                'quantity' => $m->pivot->quantity ?? 1,
                'status'   => $m->pivot->status ?? 'pending',
            ])->values(),
            'keluhans'       => ($case->keluhans ?? collect())->map(fn($k) => ['id' => $k->id, 'name' => $k->name])->values(),
            'diagnosas'      => ($case->diagnosas ?? collect())->map(fn($d) => ['id' => $d->id, 'name' => $d->name])->values(),
            'tindakans'      => ($case->tindakans ?? collect())->map(fn($t) => ['id' => $t->id, 'name' => $t->name])->values(),
        ];
    }
}
