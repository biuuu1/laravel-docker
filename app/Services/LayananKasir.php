<?php

declare(strict_types=1);

namespace App\Services;

use App\Contracts\RepositoriMember;
use App\Contracts\RepositoriProduk;
use App\Contracts\RepositoriTransaksi;
use App\Exceptions\MemberTidakDitemukan;
use App\Exceptions\PembayaranKurang;
use App\Exceptions\ProdukTidakDitemukan;
use App\Exceptions\StokTidakCukup;

final class LayananKasir
{
    public function __construct(
        private RepositoriProduk $produk,
        private RepositoriMember $member,
        private RepositoriTransaksi $transaksi,
    ) {
    }

    public function buat(
        string $nomor,
        array $items,
        int $dibayar,
        ?string $kodeMember = null,
    ): array {
        $detail = [];
        $total = 0;

        foreach ($items as $item) {
            $produk = $this->produk->cari((int) ($item['produk_id'] ?? 0));

            if ($produk === null) {
                throw new ProdukTidakDitemukan();
            }

            $jumlah = (int) ($item['jumlah'] ?? 0);

            if ($jumlah <= 0) {
                continue;
            }

            if ($jumlah > $produk['stok']) {
                throw new StokTidakCukup();
            }

            $subtotal = $produk['harga'] * $jumlah;
            $total += $subtotal;

            $detail[] = [
                'produk_id' => $produk['id'],
                'nama' => $produk['nama'],
                'harga' => $produk['harga'],
                'jumlah' => $jumlah,
                'subtotal' => $subtotal,
            ];
        }

        $dataMember = null;

        if ($kodeMember !== null) {
            $dataMember = $this->member->cari($kodeMember);

            if ($dataMember === null) {
                throw new MemberTidakDitemukan();
            }
        }

        if ($dibayar < $total) {
            throw new PembayaranKurang();
        }

        $data = [
            'nomor' => $nomor,
            'member' => $dataMember,
            'detail' => $detail,
            'total' => $total,
            'dibayar' => $dibayar,
            'kembalian' => $dibayar - $total,
        ];

        return $this->transaksi->simpan($data);
    }

    public function cari(string $nomor): ?array
    {
        return $this->transaksi->cari($nomor);
    }

    public function semua(): array
    {
        return $this->transaksi->semua();
    }
}
