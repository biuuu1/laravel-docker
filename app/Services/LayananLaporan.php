<?php

declare(strict_types=1);

namespace App\Services;

use App\Contracts\RepositoriTransaksi;

final class LayananLaporan
{
    public function __construct(
        private RepositoriTransaksi $transaksi,
    ) {
    }

    public function semuaTransaksi(): array
    {
        return $this->transaksi->semua();
    }

    public function cariTransaksi(string $nomor): ?array
    {
        return $this->transaksi->cari($nomor);
    }

    public function ringkasan(): array
    {
        $transaksi = $this->transaksi->semua();

        $totalTransaksi = count($transaksi);
        $totalPenjualan = 0;

        foreach ($transaksi as $item) {
            $totalPenjualan += (int) ($item['total'] ?? 0);
        }

        return [
            'jumlah_transaksi' => $totalTransaksi,
            'total_penjualan' => $totalPenjualan,
        ];
    }
}
