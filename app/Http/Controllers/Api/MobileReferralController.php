<?php

namespace App\Http\Controllers\Api;

use App\Models\HospitalReferral;
use Illuminate\Http\Request;

class MobileReferralController extends BaseApiController
{
    public function index(Request $request)
    {
        $query = HospitalReferral::with(['santri', 'referrer'])->latest('referral_date');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $items = $query->paginate(20)->through(fn ($referral) => $this->transformItem($referral));

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
        $validated['referred_by'] = $request->user()->id;

        $referral = HospitalReferral::create($validated);

        return $this->success([
            'item' => $this->transformItem($referral->fresh(['santri', 'referrer'])),
        ], 'Rujukan berhasil dibuat.', 201);
    }

    public function update(Request $request, HospitalReferral $referral)
    {
        $validated = $this->validatePayload($request);
        $validated['referred_by'] = $request->user()->id;

        $referral->update($validated);

        return $this->success([
            'item' => $this->transformItem($referral->fresh(['santri', 'referrer'])),
        ], 'Rujukan berhasil diperbarui.');
    }

    private function validatePayload(Request $request): array
    {
        return $request->validate([
            'santri_id' => ['required', 'exists:santris,id'],
            'hospital_name' => ['required', 'string', 'max:255'],
            'referral_date' => ['required', 'date'],
            'reason' => ['required', 'string'],
            'diagnosis' => ['nullable', 'string', 'max:255'],
            'transport' => ['nullable', 'string', 'max:100'],
            'companion_name' => ['nullable', 'string', 'max:255'],
            'status' => ['required', 'in:pending,ongoing,completed'],
            'notes' => ['nullable', 'string'],
        ]);
    }

    private function transformItem(HospitalReferral $referral): array
    {
        return [
            'id' => $referral->id,
            'hospital_name' => $referral->hospital_name,
            'referral_date' => optional($referral->referral_date)->toDateString(),
            'reason' => $referral->reason,
            'diagnosis' => $referral->diagnosis,
            'transport' => $referral->transport,
            'companion_name' => $referral->companion_name,
            'status' => $referral->status,
            'notes' => $referral->notes,
            'santri' => [
                'id' => $referral->santri?->id,
                'name' => $referral->santri?->name,
                'nis' => $referral->santri?->nis,
                'guardian_phone' => $referral->santri?->guardian_phone,
            ],
            'referrer' => $referral->referrer ? [
                'id' => $referral->referrer->id,
                'name' => $referral->referrer->name,
            ] : null,
        ];
    }
}
