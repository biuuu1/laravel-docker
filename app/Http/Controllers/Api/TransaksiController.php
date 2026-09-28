<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class TransaksiController extends Controller
{
    /**
     * Menampilkan daftar transaksi.
     */
    public function index(): JsonResponse
    {
        return response()->json([
            'data' => [],
            'message' => 'Daftar transaksi',
        ]);
    }

    /**
     * Menampilkan detail transaksi berdasarkan ID.
     */
    public function show(int $id): JsonResponse
    {
        return response()->json([
            'data' => null,
            'message' => 'Detail transaksi belum diimplementasikan',
            'id' => $id,
        ]);
    }

    /**
     * Membuat transaksi baru.
     */
    public function store(Request $request): JsonResponse
    {
        return response()->json([
            'message' => 'Pembuatan transaksi belum diimplementasikan',
            'diterima' => $request->all(),
        ], 501);
    }
}