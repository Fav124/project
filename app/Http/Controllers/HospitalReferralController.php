<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\HealthManagementValidation;
use App\Http\Controllers\Concerns\SendsGuardianWhatsApp;
use App\Models\HospitalReferral;
use App\Models\Santri;
use App\Services\WhatsAppService;
use Illuminate\Http\Request;

class HospitalReferralController extends Controller
{
    use HealthManagementValidation, SendsGuardianWhatsApp;

    public function index(Request $request)
    {
        $query = HospitalReferral::with(['santri', 'referrer']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($builder) use ($search) {
                $builder->where('hospital_name', 'like', '%' . $search . '%')
                    ->orWhereHas('santri', function ($santriQuery) use ($search) {
                        $santriQuery->where('name', 'like', '%' . $search . '%')
                            ->orWhere('nis', 'like', '%' . $search . '%');
                    });
            });
        }

        $referrals = $query->latest('referral_date')->latest()->paginate(10)->withQueryString();
        $santris = Santri::orderBy('name')->get();
        $editReferral = $request->filled('edit')
            ? HospitalReferral::find($request->edit)
            : null;
        $detailReferral = $request->filled('detail')
            ? HospitalReferral::with(['santri', 'referrer'])->find($request->detail)
            : null;
        $showForm = $request->boolean('create') || $editReferral || $request->isMethod('post');

        return view('health.referrals.index', compact('referrals', 'santris', 'editReferral', 'detailReferral', 'showForm'));
    }

    public function show(HospitalReferral $referral)
    {
        $referral->load(['santri', 'referredBy']);
        return view('health.referrals.show', compact('referral'));
    }

    public function store(Request $request, WhatsAppService $whatsApp)
    {
        $validated = $request->validate([
            'referrals' => ['required', 'array', 'min:1'],
            'referrals.*.santri_id' => ['required', 'exists:santris,id'],
            'referrals.*.hospital_name' => ['required', 'string', 'max:255'],
            'referrals.*.referral_date' => ['required', 'date'],
            'referrals.*.diagnosis' => ['required', 'string'],
            'referrals.*.reason' => ['required', 'string'],
            'referrals.*.status' => ['required', 'in:pending,ongoing,completed'],
        ]);

        foreach ($validated['referrals'] as $referralData) {
            $referralData['referred_by'] = auth()->id();
            HospitalReferral::create($referralData);
        }

        $message = count($validated['referrals']) . ' data rujukan berhasil ditambahkan.';
        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => $message]);
        }

        return redirect()->route('referrals.index')->with('success', $message);
    }

    public function update(Request $request, HospitalReferral $referral, WhatsAppService $whatsApp)
    {
        $validated = $request->validate($this->referralRules());
        $validated['referred_by'] = auth()->id();

        $referral->update($validated);

        $successMessage = 'Data rujukan rumah sakit berhasil diperbarui.';
        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => $successMessage]);
        }

        $redirect = redirect()->route('referrals.index')
            ->with('success', $successMessage);

        if ($request->boolean('notify_guardian')) {
            $result = $this->sendReferralNotification($referral->fresh(), $whatsApp);
            if ($result['success']) {
                $redirect->with('success', $successMessage . ' ' . $result['message']);
            } else {
                $redirect->with('warning', $result['message']);
            }
        }

        return $redirect;
    }

    public function notifyGuardian(HospitalReferral $referral, WhatsAppService $whatsApp)
    {
        $result = $this->sendReferralNotification($referral, $whatsApp);

        return back()->with($result['success'] ? 'success' : 'warning', $result['message']);
    }

    public function destroy(HospitalReferral $referral)
    {
        $referral->delete();

        return redirect()->route('referrals.index')
            ->with('success', 'Data rujukan rumah sakit berhasil dihapus.');
    }
}
