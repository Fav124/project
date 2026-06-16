<?php

namespace App\Http\Controllers\Api;

use App\Models\SicknessCase;
use Illuminate\Http\Request;

class MobileSicknessCaseController extends BaseApiController
{
    public function index(Request $request)
    {
        $query = SicknessCase::with(['santri', 'medicines', 'handler'])->latest('visit_date');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $items = $query->paginate(20)->through(fn ($case) => $this->transformItem($case));

        return $this->success([
            'items' => $items->items(),
            'meta' => [
                'current_page' => $items->currentPage(),
                'last_page' => $items->lastPage(),
                'total' => $items->total(),
            ],
        ]);
    }

    public function store(Request $request)
    {
        $validated = $this->validatePayload($request);
        $validated['handled_by'] = $request->user()->id;
        $medicineIds = $validated['medicine_ids'] ?? [];
        unset($validated['medicine_ids']);

        $case = SicknessCase::create($validated);
        $this->syncMedicines($case, $medicineIds);
        return $this->success([
            'item' => $this->transformItem($case->fresh(['santri', 'medicines', 'handler'])),
        ], 'Kasus sakit berhasil dibuat.', 201);
    }

    public function update(Request $request, SicknessCase $sicknessCase)
    {
        $validated = $this->validatePayload($request);
        $validated['handled_by'] = $request->user()->id;
        $medicineIds = $validated['medicine_ids'] ?? [];
        unset($validated['medicine_ids']);

        $sicknessCase->update($validated);
        $this->syncMedicines($sicknessCase, $medicineIds);

        return $this->success([
            'item' => $this->transformItem($sicknessCase->fresh(['santri', 'medicines', 'handler'])),
        ], 'Kasus sakit berhasil diperbarui.');
    }

    private function validatePayload(Request $request): array
    {
        return $request->validate([
            'santri_id' => ['required', 'exists:santris,id'],
            'visit_date' => ['required', 'date'],
            'complaint' => ['required', 'string'],
            'diagnosis' => ['nullable', 'string', 'max:255'],
            'action_taken' => ['nullable', 'string'],
            'medicine_notes' => ['nullable', 'string'],
            'status' => ['required', 'in:observed,handled,recovered,referred'],
            'return_date' => ['nullable', 'date'],
            'notes' => ['nullable', 'string'],
            'medicine_ids' => ['nullable', 'array'],
            'medicine_ids.*' => ['exists:medicines,id'],
        ]);
    }

    private function syncMedicines(SicknessCase $case, array $medicineIds): void
    {
        $payload = collect($medicineIds)->mapWithKeys(fn ($id) => [
            $id => ['quantity' => 1, 'status' => 'pending'],
        ])->all();

        $case->medicines()->sync($payload);
        $case->update(['medicine_id' => $medicineIds[0] ?? null]);
    }


    private function transformItem(SicknessCase $case): array
    {
        return [
            'id' => $case->id,
            'visit_date' => optional($case->visit_date)->toDateString(),
            'complaint' => $case->complaint,
            'diagnosis' => $case->diagnosis,
            'action_taken' => $case->action_taken,
            'medicine_notes' => $case->medicine_notes,
            'status' => $case->status,
            'return_date' => optional($case->return_date)->toDateString(),
            'notes' => $case->notes,
            'santri' => [
                'id' => $case->santri?->id,
                'name' => $case->santri?->name,
                'nis' => $case->santri?->nis,
                'guardian_phone' => $case->santri?->guardian_phone,
            ],

            'medicines' => $case->medicines->map(fn ($medicine) => [
                'id' => $medicine->id,
                'name' => $medicine->name,
                'unit' => $medicine->unit,
            ])->values(),
            'handler' => $case->handler ? [
                'id' => $case->handler->id,
                'name' => $case->handler->name,
            ] : null,
        ];
    }
}
