<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Kategori;
use App\Models\Produk;
use App\Services\LayananKatalog;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

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

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'kategori_id' => [
                'required',
                'integer',
                Rule::exists('kategori', 'id')->where('aktif', true),
            ],
            'sku' => [
                'required',
                'string',
                'max:20',
                'regex:/^SKU-[0-9]{3}$/',
                Rule::unique('produk', 'sku'),
            ],
            'nama' => ['required', 'string', 'max:150'],
            'harga' => ['required', 'integer', 'min:0'],
            'aktif' => ['sometimes', 'boolean'],
        ]);

        $produk = Produk::query()->create($data);

        return response()->json([
            'data' => $this->formatProduk($produk->fresh()),
        ], 201);
    }

    public function update(Request $request, string $sku): JsonResponse
    {
        $produk = Produk::query()->where('sku', strtoupper($sku))->firstOrFail();

        $data = $request->validate([
            'kategori_id' => [
                'sometimes',
                'required',
                'integer',
                Rule::exists('kategori', 'id')->where('aktif', true),
            ],
            'sku' => [
                'sometimes',
                'required',
                'string',
                'max:20',
                'regex:/^SKU-[0-9]{3}$/',
                Rule::unique('produk', 'sku')->ignore($produk->id),
            ],
            'nama' => ['sometimes', 'required', 'string', 'max:150'],
            'harga' => ['sometimes', 'required', 'integer', 'min:0'],
            'aktif' => ['sometimes', 'boolean'],
        ]);

        $produk->update($data);

        return response()->json([
            'data' => $this->formatProduk($produk->fresh()),
        ]);
    }

    public function destroy(string $sku): JsonResponse
    {
        $produk = Produk::query()->where('sku', strtoupper($sku))->firstOrFail();
        $produk->delete();

        return response()->json([
            'pesan' => 'Produk berhasil dihapus.',
            'sku' => $produk->sku,
        ]);
    }

    public function trashed(): JsonResponse
    {
        $produk = Produk::onlyTrashed()
            ->orderBy('nama')
            ->get()
            ->map(fn (Produk $item): array => $this->formatProduk($item));

        return response()->json(['data' => $produk]);
    }

    public function restore(string $sku): JsonResponse
    {
        $produk = Produk::onlyTrashed()
            ->where('sku', strtoupper($sku))
            ->firstOrFail();

        $produk->restore();

        return response()->json([
            'data' => $this->formatProduk($produk->fresh()),
            'pesan' => 'Produk berhasil dipulihkan.',
        ]);
    }

    private function formatProduk(Produk $produk): array
    {
        return [
            'id' => $produk->id,
            'sku' => $produk->sku,
            'nama' => $produk->nama,
            'kategori_id' => $produk->kategori_id,
            'kategori' => $produk->kategori?->kode,
            'harga' => $produk->harga,
            'stok' => $produk->stok,
            'aktif' => $produk->aktif,
            'deleted_at' => $produk->deleted_at?->toISOString(),
        ];
    }
}
