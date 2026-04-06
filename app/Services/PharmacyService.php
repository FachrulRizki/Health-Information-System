<?php

namespace App\Services;

use App\Events\QueueStatusUpdated;
use App\Models\DrugStock;
use App\Models\Prescription;
use App\Models\PrescriptionItem;
use App\Models\QueueEntry;
use App\Models\Visit;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class PharmacyService
{
    /**
     * Validate a prescription by checking stock availability for each item.
     * Requirements: 10.2, 10.3
     */
    public function validatePrescription(int $prescriptionId): array
    {
        $prescription = Prescription::with(['items.drug'])->findOrFail($prescriptionId);

        $results      = [];
        $allSufficient = true;

        foreach ($prescription->items as $item) {
            $availableQty = DrugStock::where('drug_id', $item->drug_id)
                ->where('expiry_date', '>', Carbon::today())
                ->sum('quantity');

            $isSufficient = $availableQty >= $item->quantity;
            if (! $isSufficient) $allSufficient = false;

            $results[] = [
                'prescription_item_id' => $item->id,
                'drug_name'            => $item->drug->nama ?? 'Unknown',
                'required_quantity'    => (float) $item->quantity,
                'available_quantity'   => (float) $availableQty,
                'is_sufficient'        => $isSufficient,
            ];
        }

        return ['valid' => $allSufficient, 'items' => $results];
    }

    /**
     * Dispense a drug using FIFO (oldest non-expired stock first).
     * Requirements: 10.4, 10.8
     *
     * @throws RuntimeException
     */
    public function dispenseDrug(int $prescriptionItemId, float $quantity): void
    {
        DB::transaction(function () use ($prescriptionItemId, $quantity) {
            $item = PrescriptionItem::with('drug')->findOrFail($prescriptionItemId);

            // Check oldest batch for expiry
            $oldestStock = DrugStock::where('drug_id', $item->drug_id)
                ->orderBy('expiry_date')
                ->lockForUpdate()
                ->first();

            if ($oldestStock && $oldestStock->isExpired()) {
                throw new RuntimeException(
                    "Obat '{$item->drug->nama}' (batch: {$oldestStock->batch_number}) telah kadaluarsa."
                );
            }

            $totalAvailable = DrugStock::where('drug_id', $item->drug_id)
                ->where('expiry_date', '>', Carbon::today())
                ->sum('quantity');

            if ($totalAvailable < $quantity) {
                throw new RuntimeException(
                    "Stok obat '{$item->drug->nama}' tidak mencukupi. Tersedia: {$totalAvailable}, Dibutuhkan: {$quantity}."
                );
            }

            // FIFO deduction
            $remaining = $quantity;
            $stocks = DrugStock::where('drug_id', $item->drug_id)
                ->where('expiry_date', '>', Carbon::today())
                ->orderBy('expiry_date')
                ->lockForUpdate()
                ->get();

            foreach ($stocks as $stock) {
                if ($remaining <= 0) break;
                if ($stock->quantity <= $remaining) {
                    $remaining -= $stock->quantity;
                    $stock->delete();
                } else {
                    $stock->quantity -= $remaining;
                    $stock->save();
                    $remaining = 0;
                }
            }

            $item->update(['status' => 'dispensed']);
        });
    }

    /**
     * Check expiry status of a drug stock entry.
     */
    public function checkExpiry(int $drugStockId): array
    {
        $stock           = DrugStock::findOrFail($drugStockId);
        $daysUntilExpiry = (int) Carbon::today()->diffInDays($stock->expiry_date, false);

        return [
            'expired'           => $stock->isExpired(),
            'near_expiry'       => $stock->isNearExpiry(30),
            'days_until_expiry' => $daysUntilExpiry,
        ];
    }

    /**
     * Get all stock alerts: expired, near-expiry, and low-stock.
     * Requirements: 10.6, 10.7, 10.8
     */
    public function getStockAlerts(): array
    {
        $alerts = [];

        foreach (DrugStock::with('drug')->where('expiry_date', '<=', Carbon::today())->get() as $stock) {
            $alerts[] = ['drug_id' => $stock->drug_id, 'drug_name' => $stock->drug->nama ?? 'Unknown', 'drug_stock_id' => $stock->id, 'batch_number' => $stock->batch_number, 'alert_type' => 'expired', 'details' => ['expiry_date' => $stock->expiry_date->toDateString(), 'current_quantity' => (float) $stock->quantity]];
        }

        foreach (DrugStock::with('drug')->where('expiry_date', '>', Carbon::today())->where('expiry_date', '<=', Carbon::today()->addDays(30))->get() as $stock) {
            $alerts[] = ['drug_id' => $stock->drug_id, 'drug_name' => $stock->drug->nama ?? 'Unknown', 'drug_stock_id' => $stock->id, 'batch_number' => $stock->batch_number, 'alert_type' => 'near_expiry', 'details' => ['expiry_date' => $stock->expiry_date->toDateString(), 'days_until_expiry' => (int) Carbon::today()->diffInDays($stock->expiry_date), 'current_quantity' => (float) $stock->quantity]];
        }

        foreach (DrugStock::with('drug')->where('expiry_date', '>', Carbon::today())->whereColumn('quantity', '<=', 'minimum_stock')->get() as $stock) {
            $alerts[] = ['drug_id' => $stock->drug_id, 'drug_name' => $stock->drug->nama ?? 'Unknown', 'drug_stock_id' => $stock->id, 'batch_number' => $stock->batch_number, 'alert_type' => 'low_stock', 'details' => ['current_quantity' => (float) $stock->quantity, 'minimum_stock' => (float) $stock->minimum_stock]];
        }

        return $alerts;
    }

    /**
     * Save pharmacy SOAP notes for a prescription.
     * Requirements: 10.9
     */
    public function saveSoapFarmasi(int $prescriptionId, string $notes): void
    {
        Prescription::findOrFail($prescriptionId)->update(['pharmacy_notes' => $notes]);
    }
}
