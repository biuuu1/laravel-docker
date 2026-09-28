<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;

class LaporanController extends Controller
{
    public function __construct()
    {
        //
    }

    /**
     * Menampilkan laporan.
     */
    public function index(): JsonResponse
    {
        return response()->json([
            'message' => 'Daftar laporan',
            'data' => [],
        ]);
    }
}