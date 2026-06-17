<?php

namespace App\Services;

use App\Models\Medicine;
use App\Models\MedicineBatch;
use App\Models\MedicineMutation;
use InvalidArgumentException;
use Illuminate\Support\Facades\DB;

class MedicineStockService
{
    public function recordMutation(Medicine $medicine, string $type, int $amount, ?string $notes = null, ?\DateTime $expiryDate = null): MedicineMutation
    {
        $beforeStock = $medicine->stock;

        if ($type === 'in') {
            // Add stock as a new batch
            $batch = MedicineBatch::create([
                'medicine_id' => $medicine->id,
                'batch_number' => $this->generateBatchNumber($medicine->id),
                'quantity' => $amount,
                'expiry_date' => $expiryDate ?? $medicine->expiry_date,
                'received_date' => now(),
                'notes' => $notes,
            ]);
            $medicine->increment('stock', $amount);
        } elseif ($type === 'out') {
            // Use FIFO: deduct from oldest batches first
            $this->deductFromBatches($medicine, $amount);
            $medicine->decrement('stock', $amount);
        } elseif ($type === 'adjustment') {
            // For adjustment, we create a batch with the difference
            $currentStock = $medicine->stock;
            $difference = $amount - $currentStock;
            
            if ($difference > 0) {
                // Adding stock
                $batch = MedicineBatch::create([
                    'medicine_id' => $medicine->id,
                    'batch_number' => $this->generateBatchNumber($medicine->id),
                    'quantity' => $difference,
                    'expiry_date' => $expiryDate ?? $medicine->expiry_date,
                    'received_date' => now(),
                    'notes' => $notes ?? 'Penyesuaian stok',
                ]);
            } elseif ($difference < 0) {
                // Removing stock
                $this->deductFromBatches($medicine, abs($difference));
            }
            
            $medicine->update(['stock' => $amount]);
        } else {
            throw new InvalidArgumentException('Tipe mutasi tidak valid.');
        }

        return MedicineMutation::create([
            'medicine_id'  => $medicine->id,
            'type'         => $type,
            'amount'       => $amount,
            'before_stock' => $beforeStock,
            'after_stock'  => $medicine->fresh()->stock,
            'notes'        => $notes,
        ]);
    }

    public function dispense(int $medicineId, int $quantity, ?string $notes = null): MedicineMutation
    {
        $medicine = Medicine::findOrFail($medicineId);

        return $this->recordMutation(
            $medicine,
            'out',
            $quantity,
            $notes ?? 'Pemberian obat ke santri'
        );
    }

    public function restoreDispense(int $medicineId, int $quantity, ?string $notes = null): MedicineMutation
    {
        $medicine = Medicine::findOrFail($medicineId);

        // When restoring, add as a new batch with the original expiry date
        return $this->recordMutation(
            $medicine,
            'in',
            $quantity,
            $notes ?? 'Pembatalan pemberian obat',
            $medicine->expiry_date
        );
    }

    private function deductFromBatches(Medicine $medicine, int $quantity): void
    {
        $batches = $medicine->availableBatches()->get();
        $remaining = $quantity;

        foreach ($batches as $batch) {
            if ($remaining <= 0) break;

            if ($batch->quantity >= $remaining) {
                $batch->decrement('quantity', $remaining);
                $remaining = 0;
            } else {
                $remaining -= $batch->quantity;
                $batch->update(['quantity' => 0]);
            }
        }

        if ($remaining > 0) {
            throw new InvalidArgumentException('Stok tidak mencukupi.');
        }
    }

    private function generateBatchNumber(int $medicineId): string
    {
        $prefix = 'BTH-' . str_pad($medicineId, 4, '0', STR_PAD_LEFT);
        $count = MedicineBatch::where('medicine_id', $medicineId)->count();
        return $prefix . '-' . str_pad($count + 1, 3, '0', STR_PAD_LEFT);
    }

    public function getBatchSummary(Medicine $medicine): array
    {
        $batches = $medicine->batches()->get();
        
        return [
            'total_batches' => $batches->count(),
            'available_batches' => $batches->where('quantity', '>', 0)->count(),
            'total_stock' => $batches->sum('quantity'),
            'expired_stock' => $batches->where('expiry_date', '<', now())->sum('quantity'),
            'expiring_soon_stock' => $batches->filter(function($batch) {
                return $batch->expiry_date->isFuture() && 
                       $batch->expiry_date->diffInMonths(now()) < 3;
            })->sum('quantity'),
            'batches' => $batches->map(function($batch) {
                return [
                    'id' => $batch->id,
                    'batch_number' => $batch->batch_number,
                    'quantity' => $batch->quantity,
                    'expiry_date' => $batch->expiry_date->format('Y-m-d'),
                    'received_date' => $batch->received_date->format('Y-m-d'),
                    'is_expired' => $batch->isExpired(),
                    'is_expiring_soon' => $batch->isExpiringSoon(),
                    'days_until_expiry' => $batch->days_until_expiry,
                ];
            })->values(),
        ];
    }
}
