<?php

namespace App\Services;

use App\Events\QueueStatusUpdated;
use App\Jobs\SendBPJSClaimJob;
use App\Models\Bill;
use App\Models\BillItem;
use App\Models\QueueEntry;
use App\Models\Visit;
use Illuminate\Support\Facades\DB;

class BillingService
{
    public function generateBill(int $visitId): Bill
    {
        return DB::transaction(function () use ($visitId) {
            $bill = Bill::firstOrCreate(
                ['visit_id' => $visitId],
                ['status' => 'pending', 'total_amount' => 0]
            );

            $total = $bill->items()->sum(DB::raw('unit_price * quantity'));
            $bill->update(['total_amount' => $total]);

            return $bill->fresh('items');
        });
    }

    public function addBillItem(
        int $billId,
        string $itemType,
        string $itemName,
        float $unitPrice,
        float $quantity = 1,
        ?int $itemId = null
    ): BillItem {
        return DB::transaction(function () use ($billId, $itemType, $itemName, $unitPrice, $quantity, $itemId) {
            $item = BillItem::create([
                'bill_id'    => $billId,
                'item_type'  => $itemType,
                'item_id'    => $itemId,
                'item_name'  => $itemName,
                'unit_price' => $unitPrice,
                'quantity'   => $quantity,
                'subtotal'   => $unitPrice * $quantity,
            ]);

            $bill  = Bill::findOrFail($billId);
            $total = $bill->items()->sum(DB::raw('unit_price * quantity'));
            $bill->update(['total_amount' => $total]);

            return $item;
        });
    }

    public function processPayment(int $billId, string $method, array $data = []): Bill
    {
        $bill = Bill::findOrFail($billId);

        $bill->update([
            'payment_method' => $method,
            'status'         => 'paid',
        ]);

        if ($method === 'bpjs') {
            SendBPJSClaimJob::dispatch($billId);
        }

        // Update visit status to 'selesai' after payment (Req 4.5)
        $visit = Visit::find($bill->visit_id);
        if ($visit) {
            $visit->update(['status' => 'selesai']);

            // Update queue entry to 'selesai' and fire real-time event
            $queueEntry = QueueEntry::where('visit_id', $bill->visit_id)->first();
            if ($queueEntry) {
                $queueEntry->update(['status' => 'selesai']);
                event(new QueueStatusUpdated($queueEntry->fresh()));
            }
        }

        return $bill->fresh();
    }

    public function getBillDetails(int $visitId): ?Bill
    {
        return Bill::with('items')->where('visit_id', $visitId)->first();
    }
}
