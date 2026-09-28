<?php

declare(strict_types=1);

namespace App\Contracts;

interface RepositoriTransaksi
{
    public function semua(): array;

    public function cari(string $nomor): ?array;

    public function simpan(array $transaksi): array;

    public function perbarui(string $nomor, array $transaksi): array;
}
