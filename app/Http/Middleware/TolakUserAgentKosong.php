<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Menolak request yang tidak menyertakan header User-Agent (400).
 * Dipasang sebagai alias dan hanya dipakai pada rute tertentu.
 */
final class TolakUserAgentKosong
{
    public function handle(Request $request, Closure $next): Response
    {
        if (trim((string) $request->header('User-Agent', '')) === '') {
            return response()->json([
                'kesalahan' => 'user_agent_kosong',
                'pesan' => 'Header User-Agent wajib disertakan.',
            ], 400);
        }

        return $next($request);
    }
}
