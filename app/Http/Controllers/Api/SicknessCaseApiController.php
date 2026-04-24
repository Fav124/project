<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\InfirmaryBed;
use App\Models\Medicine;
use App\Models\Santri;
use App\Models\SicknessCase;
use App\Services\WhatsAppService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SicknessCaseApiController extends Controller
{
    public function lookups()
    {
        return response()->json([
            'success' => true,
            'data'    => [
                'santris'   => Santri::orderBy('name')->get(['id', 'name', 'nis']),
                'beds'      => InfirmaryBed::where('status', 'available')->orderBy('code')->get(['id', 'code', 'room_name']),
                'medicines' => Medicine::orderBy('name')->get(['id', 'name', 'unit', 'stock']),
            ]
        ]);
    }

    public function index(Request $request)
    {
        $query = SicknessCase::with(['santri:id,name,nis,gender', 'medicines:id,name', 'bed:id,code,room_name']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

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
            'santri.dormitory', 'santri.schoolClass', 'santri.major',
            'medicines', 'bed', 'handledBy:id,name',
        ])->findOrFail($id);

        return response()->json([
            'success' => true,
            'data'    => $this->formatCaseDetail($case),
        ]);
    }

    public function store(Request $request, WhatsAppService $whatsApp)
    {
        $validated = $request->validate([
            'santri_id'        => 'required|exists:santris,id',
            'infirmary_bed_id' => 'nullable|exists:infirmary_beds,id',
            'visit_date'       => 'required|date',
            'complaint'        => 'required|string',
            'diagnosis'        => 'nullable|string|max:255',
            'action_taken'     => 'nullable|string',
            'notes'            => 'nullable|string',
            'status'           => 'required|in:observed,handled,recovered,referred',
            'medicines'        => 'nullable|array',
            'medicines.*.id'   => 'required_with:medicines|exists:medicines,id',
            'medicines.*.quantity' => 'required_with:medicines|integer|min:1',
            'notify_guardian'  => 'nullable|boolean',
        ]);

        $validated['handled_by'] = auth()->id();
        $medicinesData = $validated['medicines'] ?? [];
        $notifyGuardian = $validated['notify_guardian'] ?? false;
        unset($validated['medicines'], $validated['notify_guardian']);

        $case = SicknessCase::create($validated);

        if (!empty($medicinesData)) {
            $attachData = [];
            foreach ($medicinesData as $med) {
                $attachData[$med['id']] = ['quantity' => $med['quantity'] ?? 1, 'status' => 'pending'];
            }
            $case->medicines()->attach($attachData);
        }

        $this->syncBedStatus($case);

        if ($notifyGuardian) {
            $this->sendSicknessNotification($case->load('santri'), $whatsApp);
        }

        return response()->json([
            'success' => true,
            'message' => 'Data kasus sakit berhasil disimpan.',
            'data'    => $this->formatCaseDetail($case->load(['santri', 'medicines', 'bed', 'handledBy'])),
        ], 201);
    }

    public function update(Request $request, $id, WhatsAppService $whatsApp)
    {
        $case = SicknessCase::findOrFail($id);

        $validated = $request->validate([
            'santri_id'        => 'required|exists:santris,id',
            'infirmary_bed_id' => 'nullable|exists:infirmary_beds,id',
            'visit_date'       => 'required|date',
            'complaint'        => 'required|string',
            'diagnosis'        => 'nullable|string|max:255',
            'action_taken'     => 'nullable|string',
            'notes'            => 'nullable|string',
            'status'           => 'required|in:observed,handled,recovered,referred',
            'medicines'        => 'nullable|array',
            'medicines.*.id'   => 'required_with:medicines|exists:medicines,id',
            'medicines.*.quantity' => 'required_with:medicines|integer|min:1',
        ]);

        $oldBedId = $case->infirmary_bed_id;
        $medicinesData = $validated['medicines'] ?? [];
        unset($validated['medicines']);

        $validated['handled_by'] = auth()->id();
        $case->update($validated);

        $syncData = [];
        foreach ($medicinesData as $med) {
            $syncData[$med['id']] = ['quantity' => $med['quantity']];
        }
        $case->medicines()->sync($syncData);

        if ($oldBedId && $oldBedId !== $case->infirmary_bed_id) {
            InfirmaryBed::whereKey($oldBedId)->update(['status' => 'available', 'occupant_name' => null]);
        }
        $this->syncBedStatus($case);

        return response()->json([
            'success' => true,
            'message' => 'Data berhasil diperbarui.',
            'data'    => $this->formatCaseDetail($case->load(['santri', 'medicines', 'bed', 'handledBy'])),
        ]);
    }

    public function destroy($id)
    {
        $case = SicknessCase::findOrFail($id);

        if ($case->infirmary_bed_id) {
            InfirmaryBed::whereKey($case->infirmary_bed_id)->update(['status' => 'available', 'occupant_name' => null]);
        }

        $case->delete();

        return response()->json(['success' => true, 'message' => 'Data berhasil dihapus.']);
    }

    public function markRecovered($id)
    {
        $case = SicknessCase::findOrFail($id);
        $case->update(['status' => 'recovered', 'return_date' => now()]);
        $this->syncBedStatus($case);

        return response()->json(['success' => true, 'message' => 'Santri dinyatakan sembuh.', 'data' => ['status' => 'recovered', 'status_label' => 'Sembuh']]);
    }

    // ─── Helpers ──────────────────────────────────────────────────────────────

    private function syncBedStatus(SicknessCase $case): void
    {
        if (!$case->infirmary_bed_id) return;
        $isOccupied = !in_array($case->status, ['recovered']);
        InfirmaryBed::whereKey($case->infirmary_bed_id)->update([
            'status'        => $isOccupied ? 'occupied' : 'available',
            'occupant_name' => $isOccupied ? $case->santri?->name : null,
        ]);
    }

    private function sendSicknessNotification(SicknessCase $case, WhatsAppService $whatsApp): void
    {
        if (!$case->santri?->guardian_phone) return;
        $santri = $case->santri;
        $message = "Assalamualaikum Bapak/Ibu *{$santri->guardian_name}*,\n\n"
            . "Kami dari UKS Pondok Pesantren memberitahukan bahwa santri *{$santri->name}* sedang dalam penanganan kami.\n\n"
            . "📋 *Keluhan:* {$case->complaint}\n"
            . "🏥 *Status:* " . $this->translateStatus($case->status) . "\n"
            . "📅 *Tanggal:* " . $case->visit_date?->format('d M Y') . "\n\n"
            . "Jazakumullahu khairan. Semoga segera diberikan kesembuhan.";
        $whatsApp->sendTextMessage($santri->guardian_phone, $message);
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
            'santri'       => $case->santri ? ['id' => $case->santri->id, 'name' => $case->santri->name, 'nis' => $case->santri->nis] : null,
            'complaint'    => $case->complaint,
            'diagnosis'    => $case->diagnosis,
            'status'       => $case->status,
            'status_label' => $this->translateStatus($case->status),
            'visit_date'   => $case->visit_date?->toDateString(),
            'medicines'    => $case->medicines->map(fn($m) => ['id' => $m->id, 'name' => $m->name])->values(),
        ];
    }

    private function formatCaseDetail($case): array
    {
        return [
            'id'             => $case->id,
            'santri'         => $case->santri ? [
                'id'           => $case->santri->id,
                'name'         => $case->santri->name,
                'nis'          => $case->santri->nis,
                'gender'       => $case->santri->gender,
                'dormitory'    => $case->santri->dormitory?->name,
                'class'        => $case->santri->schoolClass?->name,
                'guardian_name'  => $case->santri->guardian_name,
                'guardian_phone' => $case->santri->guardian_phone,
            ] : null,
            'complaint'      => $case->complaint,
            'diagnosis'      => $case->diagnosis,
            'action_taken'   => $case->action_taken,
            'notes'          => $case->notes,
            'status'         => $case->status,
            'status_label'   => $this->translateStatus($case->status),
            'visit_date'     => $case->visit_date?->toDateString(),
            'return_date'    => $case->return_date?->toDateString(),
            'handled_by'     => $case->handledBy?->name,
            'bed'            => $case->bed ? ['id' => $case->bed->id, 'code' => $case->bed->code, 'room' => $case->bed->room_name] : null,
            'medicines'      => $case->medicines->map(fn($m) => [
                'id' => $m->id, 'name' => $m->name, 'unit' => $m->unit,
                'quantity' => $m->pivot->quantity ?? 1,
                'status'   => $m->pivot->status ?? 'pending',
            ])->values(),
        ];
    }
}
