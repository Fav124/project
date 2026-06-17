<?php

namespace App\Http\Controllers\Concerns;

trait HealthManagementValidation
{
    protected function santriRules($santriId = null): array
    {
        $nisRule = 'nullable|string|max:50|unique:santris,nis';

        if ($santriId) {
            $nisRule .= ',' . $santriId;
        }

        return [
            'nis' => $nisRule,
            'name' => ['required', 'string', 'max:255'],
            'gender' => ['required', 'in:L,P'],
            'birth_place' => ['nullable', 'string', 'max:255'],
            'birth_date' => ['nullable', 'date'],
            'class_id' => ['nullable', 'exists:classes,id'],
            'major_id' => ['nullable', 'exists:majors,id'],
            'class_room' => ['nullable', 'string', 'max:100'],
            'guardian_name' => ['nullable', 'string', 'max:255'],
            'guardian_phone' => ['nullable', 'string', 'max:50'],
            'guardian_relationship' => ['nullable', 'string', 'max:100'],
            'guardian_job' => ['nullable', 'string', 'max:100'],
            'guardian_address' => ['nullable', 'string'],
            'blood_type' => ['nullable', 'string', 'max:5'],
            'blood_pressure' => ['nullable', 'string', 'max:20'],
            'height' => ['nullable', 'numeric', 'min:0'],
            'weight' => ['nullable', 'numeric', 'min:0'],
            'allergies' => ['nullable', 'string'],
            'medical_history' => ['nullable', 'string'],
            'special_condition' => ['nullable', 'string'],
            'notes' => ['nullable', 'string'],
        ];
    }

    protected function medicineRules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'unit' => ['required', 'string', 'max:50'],
            'stock' => ['required', 'integer', 'min:0'],
            'minimum_stock' => ['required', 'integer', 'min:0'],
            'expiry_date' => ['nullable', 'date'],
            'description' => ['nullable', 'string'],
        ];
    }

    protected function healthRecordRules(): array
    {
        return [
            'santri_id' => ['required', 'exists:santris,id'],
            'record_date' => ['required', 'date'],
            'complaint' => ['required', 'string'],
            'diagnosis' => ['nullable', 'string', 'max:255'],
            'treatment' => ['nullable', 'string'],
            'blood_pressure' => ['nullable', 'string', 'max:50'],
            'temperature' => ['nullable', 'numeric', 'between:30,45'],
            'pulse' => ['nullable', 'integer', 'min:0'],
            'weight' => ['nullable', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string'],
        ];
    }

    protected function sicknessCaseRules(): array
    {
        return [
            'santri_id' => ['required', 'exists:santris,id'],
            'medicine_id' => ['nullable', 'exists:medicines,id'],
            'visit_date' => ['required', 'date'],
            'complaint' => ['required', 'string'],
            'diagnosis' => ['nullable', 'string', 'max:255'],
            'action_taken' => ['nullable', 'string'],
            'medicine_notes' => ['nullable', 'string'],
            'status' => ['required', 'in:observed,handled,recovered,referred'],
            'return_date' => ['nullable', 'date'],
            'notes' => ['nullable', 'string'],
        ];
    }

    protected function referralRules(): array
    {
        return [
            'santri_id' => ['required', 'exists:santris,id'],
            'hospital_name' => ['required', 'string', 'max:255'],
            'referral_date' => ['required', 'date'],
            'reason' => ['required', 'string'],
            'diagnosis' => ['nullable', 'string', 'max:255'],
            'transport' => ['nullable', 'string', 'max:100'],
            'companion_name' => ['nullable', 'string', 'max:255'],
            'status' => ['required', 'in:pending,ongoing,completed'],
            'notes' => ['nullable', 'string'],
        ];
    }
}
