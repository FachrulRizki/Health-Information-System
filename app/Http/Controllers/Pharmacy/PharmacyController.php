<?php

namespace App\Http\Controllers\Pharmacy;

use App\Http\Controllers\Controller;
use App\Models\Prescription;
use App\Services\PharmacyService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PharmacyController extends Controller
{
    public function __construct(private PharmacyService $pharmacyService) {}

    public function index(): View
    {
        $prescriptions = Prescription::with(['visit.patient', 'visit.poli', 'prescriber'])
            ->whereIn('status', ['pending', 'validated'])
            ->orderByDesc('created_at')
            ->get();

        return view('pharmacy.index', compact('prescriptions'));
    }

    public function show(int $prescriptionId): View
    {
        $prescription     = Prescription::with(['visit.patient', 'items.drug', 'prescriber'])->findOrFail($prescriptionId);
        $validationResult = $this->pharmacyService->validatePrescription($prescriptionId);

        return view('pharmacy.show', compact('prescription', 'validationResult'));
    }

    public function validate(int $prescriptionId): RedirectResponse
    {
        $result = $this->pharmacyService->validatePrescription($prescriptionId);

        if ($result['valid']) {
            Prescription::findOrFail($prescriptionId)->update(['status' => 'validated']);
            return redirect()->route('pharmacy.show', $prescriptionId)->with('success', 'Resep berhasil divalidasi.');
        }

        return redirect()->route('pharmacy.show', $prescriptionId)->with('warning', 'Stok tidak mencukupi untuk beberapa item resep.');
    }

    public function dispense(Request $request, int $prescriptionId): RedirectResponse
    {
        $prescription = Prescription::with(['items', 'visit'])->findOrFail($prescriptionId);

        try {
            foreach ($prescription->items as $item) {
                $this->pharmacyService->dispenseDrug($item->id, (float) $item->quantity);
            }
            $prescription->update(['status' => 'dispensed']);

            // Update visit status to 'kasir' after all drugs dispensed (Req 4.5)
            if ($prescription->visit && $prescription->visit->status === 'farmasi') {
                $prescription->visit->update(['status' => 'kasir']);

                // Fire QueueStatusUpdated so real-time display reflects the change
                $queueEntry = \App\Models\QueueEntry::where('visit_id', $prescription->visit_id)->first();
                if ($queueEntry) {
                    event(new \App\Events\QueueStatusUpdated($queueEntry->fresh()));
                }
            }

            return redirect()->route('pharmacy.index')->with('success', 'Obat berhasil diserahkan kepada pasien.');
        } catch (\RuntimeException $e) {
            return redirect()->route('pharmacy.show', $prescriptionId)->with('error', $e->getMessage());
        }
    }

    public function stock(): View
    {
        $drugs = \App\Models\Drug::where('is_active', true)
            ->with(['stocks' => fn($q) => $q->orderBy('expiry_date'), 'stocks.drug'])
            ->orderBy('nama')
            ->get()
            ->map(function ($drug) {
                $totalStock   = $drug->stocks->sum('quantity');
                $minStock     = $drug->stocks->min('minimum_stock') ?? 0;
                $hasExpired   = $drug->stocks->filter(fn($s) => $s->isExpired())->isNotEmpty();
                $hasNearExpiry = $drug->stocks->filter(fn($s) => $s->isNearExpiry(30))->isNotEmpty();
                $isLow        = $totalStock <= $minStock && $minStock > 0;

                $alert = null;
                if ($hasExpired) {
                    $alert = 'expired';
                } elseif ($hasNearExpiry) {
                    $alert = 'near_expiry';
                } elseif ($isLow) {
                    $alert = 'low_stock';
                }

                return [
                    'drug'        => $drug,
                    'total_stock' => $totalStock,
                    'min_stock'   => $minStock,
                    'alert'       => $alert,
                    'stocks'      => $drug->stocks,
                ];
            });

        $hasAlerts = $drugs->whereNotNull('alert')->isNotEmpty();

        return view('pharmacy.stock', compact('drugs', 'hasAlerts'));
    }

    public function soapFarmasi(Request $request, int $prescriptionId): RedirectResponse
    {
        $request->validate(['pharmacy_notes' => 'required|string|max:2000']);
        $this->pharmacyService->saveSoapFarmasi($prescriptionId, $request->pharmacy_notes);
        return redirect()->route('pharmacy.show', $prescriptionId)->with('success', 'Catatan SOAP farmasi berhasil disimpan.');
    }
}
