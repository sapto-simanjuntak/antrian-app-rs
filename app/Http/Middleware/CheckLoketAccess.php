<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckLoketAccess
{
    public function handle(Request $request, Closure $next): Response
    {
        $user    = Auth::user();
        $loketId = (int) $request->route('loketId');

        if (! $user->canAccessLoket($loketId)) {
            // Operator yang mencoba akses loket lain → redirect ke loket mereka sendiri
            return redirect()
                ->route('loket.index', $user->loket_id)
                ->with('error', 'Anda tidak memiliki akses ke loket tersebut.');
        }

        return $next($request);
    }
}
