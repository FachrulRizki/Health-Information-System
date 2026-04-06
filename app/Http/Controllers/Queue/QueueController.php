<?php

namespace App\Http\Controllers\Queue;

use App\Http\Controllers\Controller;
use App\Models\Poli;
use App\Services\QueueService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class QueueController extends Controller
{
    public function __construct(private QueueService $queueService) {}

    public function index(Request $request)
    {
        $polis  = Poli::where('is_active', true)->orderBy('nama_poli')->get();
        $poliId = (int) $request->query('poli_id', $polis->first()?->id ?? 0);
        $poli   = $polis->firstWhere('id', $poliId);
        $queue  = $poliId ? $this->queueService->getQueueByPoli($poliId) : collect();

        return view('queue.index', compact('polis', 'poli', 'poliId', 'queue'));
    }

    public function updateStatus(Request $request, int $queueId): JsonResponse
    {
        $validated = $request->validate([
            'status' => 'required|in:menunggu,dipanggil,dalam_pemeriksaan,selesai',
        ]);

        $entry = $this->queueService->updateStatus($queueId, $validated['status']);

        return response()->json([
            'success'      => true,
            'queue_id'     => $entry->id,
            'status'       => $entry->status,
            'patient_name' => $entry->visit?->patient?->nama_lengkap,
            'queue_number' => $entry->queue_number,
        ]);
    }

    public function display(int $poliId)
    {
        $poli    = Poli::findOrFail($poliId);
        $queue   = $this->queueService->getQueueByPoli($poliId);
        $current = $queue->firstWhere('status', 'dipanggil');
        $waiting = $queue->where('status', 'menunggu')->take(5);

        return view('queue.display', compact('poli', 'current', 'waiting', 'poliId'));
    }
}
