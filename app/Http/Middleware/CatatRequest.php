<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

final class CatatRequest
{
    public function handle(Request $request, Closure $next): Response
    {
        $mulai = microtime(true);

        // ---- SEBELUM controller dijalankan ----
        $response = $next($request);

        // ---- SESUDAH controller, sebelum response dikirim ----
        $durasiMs = round((microtime(true) - $mulai) * 1000, 2);

        logger()->info('HTTP', [
            'method' => $request->method(),
            'uri' => $request->path(),
            'status' => $response->getStatusCode(),
            'durasi_ms'=> $durasiMs,
            'ip' => $request->ip(),
        ]);

        // Tambahkan header khusus pada response
        $response->headers->set('X-Response-Time', $durasiMs . 'ms');

        return $response;
    }
}