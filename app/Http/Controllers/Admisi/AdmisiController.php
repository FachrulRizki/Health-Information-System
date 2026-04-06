<?php

namespace App\Http\Controllers\Admisi;

use App\Http\Controllers\Controller;
use App\Models\Bed;
use App\Models\Room;
use App\Models\Visit;
use App\Services\BedManagementService;
use Illuminate\Http\Request;

class AdmisiController extends Controller
{
    public function __construct(private BedManagementService $bedService) {}

    public function index()
    {
        $dateFrom = request()->query('date_from', today()->toDateString());
        $dateTo   = request()->query('date_to', today()->toDateString());

        $visits = Visit::with(['patient', 'poli', 'doctor', 'queueEntry'])
            ->whereIn('status', ['kasir', 'selesai', 'dalam_pemeriksaan', 'farmasi'])
            ->whereDoesntHave('inpatientRecord')
            ->whereDate('tanggal_kunjungan', '>=', $dateFrom)
            ->whereDate('tanggal_kunjungan', '<=', $dateTo)
            ->orderByDesc('created_at')
            ->get();

        return view('admisi.index', compact('visits', 'dateFrom', 'dateTo'));
    }

    public function confirm(int $visitId)
    {
        $visit = Visit::with(['patient', 'poli', 'doctor'])->findOrFail($visitId);

        $rooms = Room::with(['beds' => fn($q) => $q->whereIn('status', ['tersedia', 'available'])])
            ->where('is_active', true)
            ->get()
            ->filter(fn($room) => $room->beds->count() > 0);

        $availableBeds = Bed::with('room')
            ->whereIn('status', ['tersedia', 'available'])
            ->get();

        return view('admisi.confirm', compact('visit', 'rooms', 'availableBeds'));
    }

    public function store(Request $request, int $visitId)
    {
        $request->validate([
            'bed_id'          => 'required|exists:beds,id',
            'catatan_admisi'  => 'nullable|string|max:500',
        ]);

        $visit = Visit::findOrFail($visitId);
        $this->bedService->assignBed($visitId, (int) $request->bed_id);
        $visit->update(['status' => 'rawat_inap']);

        return redirect()->route('admisi.index')
            ->with('success', "Pasien {$visit->patient?->nama_lengkap} berhasil diadmisi.");
    }
}
