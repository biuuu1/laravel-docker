<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Services\LayananKatalog;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class ProdukController extends Controller
{
    public function __construct(
        private readonly LayananKatalog $katalog,
    ) {}

    public function index(Request $request): JsonResponse
    {
        $kategori = $request->string('kategori')->trim()->toString() ?: null;
        $cari = $request->string('cari')->trim()->toString() ?: null;

        return response()->json([
            'data' => $this->katalog->daftar($kategori, $cari),
        ]);
    }

    public function show(string $sku): JsonResponse
    {
        return response()->json([
            'data' => $this->katalog->ambil($sku),
        ]);
    }
}
