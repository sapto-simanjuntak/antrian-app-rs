<?php

namespace App\Http\Controllers;

use App\Services\QueueService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class LoketController extends Controller
{
    public function __construct(private readonly QueueService $queueService) {}

    /**
     * Halaman operator loket.
     */
    public function index(int $loketId)
    {
        abort_unless(in_array($loketId, [1, 2, 3]), 404);

        $loketInfo = \App\Models\Queue::loketInfo($loketId);
        $user      = Auth::user();

        return view('loket.index', compact('loketId', 'loketInfo', 'user'));
    }

    /**
     * GET: Ambil state terkini loket (polling).
     */
    public function state(int $loketId): JsonResponse
    {
        return response()->json($this->queueService->getLoketState($loketId));
    }

    /**
     * POST: Panggil antrian berikutnya.
     */
    public function panggil(int $loketId): JsonResponse
    {
        return $this->respond($this->queueService->panggil($loketId));
    }

    /**
     * POST: Panggil ulang antrian aktif.
     */
    public function panggilUlang(int $loketId): JsonResponse
    {
        return $this->respond($this->queueService->panggilUlang($loketId));
    }

    /**
     * POST: Tunda pelayanan.
     */
    public function pause(int $loketId): JsonResponse
    {
        return $this->respond($this->queueService->pause($loketId));
    }

    /**
     * POST: Pelayanan selesai.
     */
    public function selesai(int $loketId): JsonResponse
    {
        return $this->respond($this->queueService->selesai($loketId));
    }

    /**
     * POST: Batalkan antrian aktif.
     */
    public function batal(int $loketId): JsonResponse
    {
        return $this->respond($this->queueService->batal($loketId));
    }

    /**
     * POST: Tandai pasien tidak hadir (no-show).
     */
    public function tidakHadir(int $loketId): JsonResponse
    {
        return $this->respond($this->queueService->tidakHadir($loketId));
    }

    // ── Helper ────────────────────────────────────────────────────────────────

    private function respond(array $result): JsonResponse
    {
        $status = $result['success'] ? 200 : 422;
        return response()->json($result, $status);
    }
}
