<?php

namespace App\Http\Controllers;

use App\Services\QueueService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class KioskController extends Controller
{
    public function __construct(private readonly QueueService $queueService) {}

    /**
     * Halaman terminal pasien.
     */
    public function index()
    {
        $stats = $this->queueService->getKioskStats();
        return view('kiosk.index', compact('stats'));
    }

    /**
     * POST: Ambil nomor antrian baru.
     */
    public function ambil(Request $request): JsonResponse
    {
        $request->validate(['loket_id' => 'required|integer|in:1,2,3']);

        try {
            $loketId = (int) $request->loket_id;
            $queue   = $this->queueService->ambilAntrian($loketId);

            $waitingCount = \App\Models\Queue::today()->loket($loketId)->waiting()->count();

            return response()->json([
                'success' => true,
                'data'    => array_merge($queue->toApiArray(), ['waiting_count' => $waitingCount]),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil antrian. Silakan coba lagi.',
            ], 500);
        }
    }
}
