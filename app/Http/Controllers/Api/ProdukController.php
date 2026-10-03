<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\LayananProduk;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class ProdukController extends Controller
{
    // Laravel menyuntikkan LayananProduk secara otomatis
    public function __construct(
        private readonly LayananProduk $layanan,
    ) {}

    public function index(): JsonResponse
    {
        return response()->json([
            'data' => $this->layanan->semua(),
        ]);
    }

    public function show(int $id): JsonResponse
    {
        $produk = $this->layanan->cari($id);

        if ($produk === null) {
            return response()->json([
                'message' => 'Produk tidak ditemukan',
            ], 404);
        }

        return response()->json(['data' => $produk]);
    }

    public function store(Request $request): JsonResponse
    {
        // Validasi lengkap akan dibahas pada Modul 7
        return response()->json([
            'message' => 'Belum diimplementasikan',
            'diterima' => $request->all(),
        ], 501);
    }
}
