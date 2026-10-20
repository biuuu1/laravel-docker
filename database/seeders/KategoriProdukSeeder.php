<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Domain\Kategori as EnumKategori;
use App\Models\Kategori;
use App\Models\Produk;
use Illuminate\Database\Seeder;
use RuntimeException;

final class KategoriProdukSeeder extends Seeder
{
    private const PRODUK = [
        ['SKU-001', 'Beras Pandan Wangi 5 kg', 'kebutuhan_rumah', 72000, 40],
        ['SKU-002', 'Minyak Goreng 1 L', 'kebutuhan_rumah', 18500, 120],
        ['SKU-003', 'Gula Pasir 1 kg', 'kebutuhan_rumah', 15500, 85],
        ['SKU-004', 'Kopi Bubuk 200 g', 'minuman', 24000, 60],
        ['SKU-005', 'Teh Celup 25 sachet', 'minuman', 9500, 0],
        ['SKU-006', 'Mie Instan Goreng', 'makanan', 3400, 480],
        ['SKU-007', 'Susu UHT 1 L', 'minuman', 19000, 36],
        ['SKU-008', 'Sabun Mandi Batang', 'kebutuhan_rumah', 5200, 150],
        ['SKU-009', 'Buku Tulis 38 lembar', 'alat_tulis', 4800, 200],
        ['SKU-010', 'Pena Gel Hitam', 'alat_tulis', 3900, 240],
    ];

    public function run(): void
    {
        $kategori = [];

        foreach (EnumKategori::cases() as $kasus) {
            $kategori[$kasus->value] = Kategori::updateOrCreate(
                ['kode' => $kasus->value],
                ['nama' => $kasus->label(), 'aktif' => true],
            );
        }

        foreach (self::PRODUK as [$sku, $nama, $kode, $harga, $stok]) {
            $produk = Produk::updateOrCreate(
                ['sku' => $sku],
                [
                    'kategori_id' => $kategori[$kode]->id,
                    'nama' => $nama,
                    'harga' => $harga,
                    'aktif' => true,
                ],
            );

            if ($produk->wasRecentlyCreated) {
                $produk->forceFill(['stok' => $stok])->save();
            }
        }

        // Pastikan produk factory tidak dibuat berulang kali.
        $produkAcuan = array_column(self::PRODUK, 0);

        $produkAcak = Produk::query()
            ->whereNotIn('sku', $produkAcuan)
            ->get(['sku', 'kategori_id', 'harga', 'stok']);

        if ($produkAcak->isEmpty()) {
            Produk::factory()->count(20)->create([
                'kategori_id' => $kategori['makanan']->id,
            ]);

            Produk::factory()->grosir()->count(4)->create([
                'kategori_id' => $kategori['minuman']->id,
            ]);

            Produk::factory()->habis()->count(2)->create([
                'kategori_id' => $kategori['alat_tulis']->id,
            ]);
        } else {
            $perKategori = $produkAcak->groupBy('kategori_id');

            $makanan = $perKategori->get($kategori['makanan']->id, collect());
            $minuman = $perKategori->get($kategori['minuman']->id, collect());
            $alatTulis = $perKategori->get($kategori['alat_tulis']->id, collect());

            $lengkap =
                $produkAcak->count() === 26
                && $makanan->count() === 20
                && $minuman->count() === 4
                && $alatTulis->count() === 2
                && $makanan->every(fn ($p) => $p->stok >= 5 && $p->stok <= 300)
                && $minuman->every(
                    fn ($p) => $p->harga >= 2000
                        && $p->harga <= 6000
                        && $p->stok >= 300
                        && $p->stok <= 900
                )
                && $alatTulis->every(fn ($p) => $p->stok === 0);

            if (! $lengkap) {
                throw new RuntimeException(
                    'Data produk factory tidak lengkap atau tidak sesuai pola seeder. '
                    . 'Seeder dihentikan agar tidak membuat produk ganda.'
                );
            }
        }
    }
}
