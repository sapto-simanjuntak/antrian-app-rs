<?php

namespace App\Http\Controllers;

use App\Services\QueueService;
use Illuminate\Http\JsonResponse;

class DisplayController extends Controller
{
    public function __construct(private readonly QueueService $queueService) {}

    /**
     * Halaman Display TV (full screen).
     */
    public function index()
    {
        return view('display.index');
    }

    /**
     * GET: State semua loket untuk display — polling endpoint.
     */
    public function state(): JsonResponse
    {
        return response()->json($this->queueService->getDisplayState());
    }
}
