<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Services\LayananKasir;
use Illuminate\Database\Seeder;

final class TransaksiContohSeeder extends Seeder
{
    public function run(LayananKasir $kasir): void
    {
        // Struk 1 — member, tunai, satu baris memenuhi syarat diskon grosir
        $kasir->proses([
            'item' => [
                ['sku' => 'SKU-006', 'kuantitas' => 12],
                ['sku' => 'SKU-002', 'kuantitas' => 2],
                ['sku' => 'SKU-004', 'kuantitas' => 1],
            ],
            'member' => true,
            'metode_bayar' => 'tunai',
            'dibayar' => 150_000,
        ], 'Bambang Saputra');

        // Struk 2 — non-member, QRIS (dianggap selalu dibayar pas)
        $kasir->proses([
            'item' => [
                ['sku' => 'SKU-001', 'kuantitas' => 1],
                ['sku' => 'SKU-007', 'kuantitas' => 3],
            ],
            'metode_bayar' => 'qris',
        ], 'Bambang Saputra');

        // Struk 3 — dibuat lalu dibatalkan, untuk menguji laporan
        // dan pengembalian stok (AB-11).
        $struk = $kasir->proses([
            'item' => [
                ['sku' => 'SKU-009', 'kuantitas' => 20],
                ['sku' => 'SKU-010', 'kuantitas' => 20],
            ],
            'metode_bayar' => 'kartu_debit',
        ], 'Bambang Saputra');

        $kasir->batalkan(
            $struk['nomor'],
            'Pesanan grosir dibatalkan pembeli',
            'Bagas Prakoso'
        );
    }
}
