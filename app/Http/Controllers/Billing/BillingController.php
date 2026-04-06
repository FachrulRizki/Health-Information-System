<?php

namespace App\Http\Controllers\Billing;

use App\Http\Controllers\Controller;
use App\Models\Bill;
use App\Models\Visit;
use App\Services\BillingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BillingController extends Controller
{
    public function __construct(private BillingService $billingService) {}

    public function index(Request $request): View
    {
        $filter = $request->query('filter', 'all'); // all, pending, paid

        $query = Visit::with(['patient', 'poli', 'bill'])
            ->whereDate('tanggal_kunjungan', today())
            ->orderByDesc('created_at');

        if ($filter === 'pending') {
            $query->where(function ($q) {
                $q->whereHas('bill', fn($b) => $b->where('status', 'pending'))
                  ->orWhereDoesntHave('bill');
            });
        } elseif ($filter === 'paid') {
            $query->whereHas('bill', fn($b) => $b->where('status', 'paid'));
        }

        $visits = $query->paginate(20)->withQueryString();

        return view('billing.index', compact('visits', 'filter'));
    }

    public function show(int $visitId): View
    {
        $visit = Visit::with(['patient', 'poli', 'doctor'])->findOrFail($visitId);
        $bill  = $this->billingService->getBillDetails($visitId)
            ?? $this->billingService->generateBill($visitId);

        return view('billing.show', compact('visit', 'bill'));
    }

    public function processPayment(Request $request, int $billId): RedirectResponse
    {
        $request->validate(['payment_method' => 'required|in:umum,bpjs,asuransi']);

        $bill = $this->billingService->processPayment($billId, $request->payment_method);

        return redirect()->route('billing.index')
            ->with('success', 'Pembayaran berhasil diproses' . ($bill->payment_method === 'bpjs' ? ' dan klaim BPJS sedang dikirim.' : '.'));
    }

    public function claims(): View
    {
        $bills = Bill::with(['visit.patient'])
            ->whereNotNull('bpjs_claim_status')
            ->orderByDesc('updated_at')
            ->get();

        return view('billing.claims', compact('bills'));
    }
}
