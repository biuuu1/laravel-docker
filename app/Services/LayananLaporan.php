<?php

declare(strict_types=1);

namespace App\Services;

use App\Contracts\RepositoriTransaksi;

final class LayananLaporan
{
    public function __construct(
        private readonly RepositoriTransaksi $transaksi,
    ) {}

    public function harian(string $tanggal): array
    {
        $transaksi = array_values(array_filter(
            $this->transaksi->semua(),
            static fn (array $t): bool => str_starts_with(
                $t['waktu'],
                $tanggal,
            ) && ($t['status'] ?? 'selesai') !== 'batal',
        ));

        $jumlahTransaksi = count($transaksi);
        $totalPenjualan = 0;

        foreach ($transaksi as $item) {
            $totalPenjualan += (int) ($item['total_bayar'] ?? 0);
        }

        return [
            'tanggal' => $tanggal,
            'jumlah_transaksi' => $jumlahTransaksi,
            'total_penjualan' => $totalPenjualan,
        ];
    }

    public function terlaris(string $tanggal, int $batas = 5): array
    {
        $transaksi = array_filter(
            $this->transaksi->semua(),
            static fn (array $t): bool => str_starts_with(
                $t['waktu'],
                $tanggal,
            ) && ($t['status'] ?? 'selesai') !== 'batal',
        );

        $produk = [];

        foreach ($transaksi as $transaksiItem) {
            foreach ($transaksiItem['item'] ?? [] as $item) {
                $sku = $item['sku'];

                if (! isset($produk[$sku])) {
                    $produk[$sku] = [
                        'sku' => $sku,
                        'nama' => $item['nama'],
                        'kuantitas' => 0,
                        'total' => 0,
                    ];
                }

                $produk[$sku]['kuantitas'] += (int) $item['kuantitas'];
                $produk[$sku]['total'] += (int) $item['total'];
            }
        }

        usort(
            $produk,
            static fn (array $a, array $b): int => $b['kuantitas'] <=> $a['kuantitas'],
        );

        return array_slice($produk, 0, $batas);
    }
}
