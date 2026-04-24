<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\HospitalReferral;
use App\Services\WhatsAppService;
use Illuminate\Http\Request;

class HospitalReferralApiController extends Controller
{
    public function index(Request $request)
    {
        $query = HospitalReferral::with(['santri:id,name,nis,gender', 'referredBy:id,name']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $s = $request->search;
            $query->whereHas('santri', fn($b) => $b->where('name', 'like', "%$s%"));
        }

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('referral_date', [$request->start_date, $request->end_date]);
        }

        $referrals = $query->latest('referral_date')->paginate((int) $request->input('per_page', 15));

        return response()->json([
            'success' => true,
            'data'    => $referrals->map(fn($r) => $this->format($r)),
            'meta'    => ['current_page' => $referrals->currentPage(), 'last_page' => $referrals->lastPage(), 'total' => $referrals->total()],
        ]);
    }

    public function show($id)
    {
        $referral = HospitalReferral::with(['santri.dormitory', 'santri.schoolClass', 'referredBy:id,name'])->findOrFail($id);
        return response()->json(['success' => true, 'data' => $this->formatDetail($referral)]);
    }

    public function store(Request $request, WhatsAppService $whatsApp)
    {
        $validated = $request->validate([
            'santri_id'      => 'required|exists:santris,id',
            'hospital_name'  => 'required|string|max:255',
            'referral_date'  => 'required|date',
            'complaint'      => 'required|string',
            'diagnosis'      => 'nullable|string|max:255',
            'transport'      => 'nullable|string|max:100',
            'companion_name' => 'nullable|string|max:100',
            'status'         => 'nullable|in:referred,treated,returned',
            'notes'          => 'nullable|string',
            'notify_guardian' => 'nullable|boolean',
        ]);

        $notifyGuardian = $validated['notify_guardian'] ?? false;
        unset($validated['notify_guardian']);
        $validated['referred_by'] = auth()->id();
        $validated['status'] = $validated['status'] ?? 'referred';

        $referral = HospitalReferral::create($validated);

        if ($notifyGuardian) {
            $referral->load('santri');
            $this->sendReferralNotification($referral, $whatsApp);
        }

        return response()->json([
            'success' => true,
            'message' => 'Rujukan RS berhasil dibuat.',
            'data'    => $this->formatDetail($referral->load(['santri', 'referredBy'])),
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $referral = HospitalReferral::findOrFail($id);

        $validated = $request->validate([
            'santri_id'      => 'required|exists:santris,id',
            'hospital_name'  => 'required|string|max:255',
            'referral_date'  => 'required|date',
            'complaint'      => 'required|string',
            'diagnosis'      => 'nullable|string|max:255',
            'transport'      => 'nullable|string|max:100',
            'companion_name' => 'nullable|string|max:100',
            'status'         => 'required|in:referred,treated,returned',
            'notes'          => 'nullable|string',
        ]);

        $validated['referred_by'] = auth()->id();
        $referral->update($validated);

        return response()->json(['success' => true, 'message' => 'Rujukan diperbarui.', 'data' => $this->formatDetail($referral->load(['santri', 'referredBy']))]);
    }

    public function destroy($id)
    {
        HospitalReferral::findOrFail($id)->delete();
        return response()->json(['success' => true, 'message' => 'Rujukan berhasil dihapus.']);
    }

    private function sendReferralNotification(HospitalReferral $referral, WhatsAppService $whatsApp): void
    {
        if (!$referral->santri?->guardian_phone) return;
        $santri = $referral->santri;
        $message = "Assalamualaikum Bapak/Ibu *{$santri->guardian_name}*,\n\n"
            . "Kami memberitahukan bahwa santri *{$santri->name}* telah dirujuk ke:\n\n"
            . "🏥 *RS:* {$referral->hospital_name}\n"
            . "📋 *Keluhan:* {$referral->complaint}\n"
            . "📅 *Tanggal:* " . \Carbon\Carbon::parse($referral->referral_date)->format('d M Y') . "\n"
            . ($referral->companion_name ? "👤 *Pendamping:* {$referral->companion_name}\n" : "")
            . "\nJazakumullahu khairan.";
        $whatsApp->sendTextMessage($santri->guardian_phone, $message);
    }

    private function format($r): array
    {
        return [
            'id'            => $r->id,
            'santri'        => $r->santri ? ['id' => $r->santri->id, 'name' => $r->santri->name, 'nis' => $r->santri->nis] : null,
            'hospital_name' => $r->hospital_name,
            'referral_date' => \Carbon\Carbon::parse($r->referral_date)->toDateString(),
            'complaint'     => $r->complaint,
            'diagnosis'     => $r->diagnosis,
            'status'        => $r->status,
            'status_label'  => $this->translateStatus($r->status),
        ];
    }

    private function formatDetail($r): array
    {
        return array_merge($this->format($r), [
            'transport'      => $r->transport,
            'companion_name' => $r->companion_name,
            'notes'          => $r->notes,
            'referred_by'    => $r->referredBy?->name,
            'santri'         => $r->santri ? [
                'id'             => $r->santri->id,
                'name'           => $r->santri->name,
                'nis'            => $r->santri->nis,
                'gender'         => $r->santri->gender,
                'dormitory'      => $r->santri->dormitory?->name,
                'class'          => $r->santri->schoolClass?->name,
                'guardian_name'  => $r->santri->guardian_name,
                'guardian_phone' => $r->santri->guardian_phone,
            ] : null,
        ]);
    }

    private function translateStatus(string $status): string
    {
        return match ($status) {
            'referred' => 'Dirujuk', 'treated' => 'Dalam Perawatan', 'returned' => 'Dipulangkan',
            default    => ucfirst($status),
        };
    }
}
