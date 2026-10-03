<?php

declare(strict_types=1);

namespace App\Contracts;

interface RepositoriTransaksi
{
    public function semua(): array;

    public function cariNomor(string $nomor): ?array;

    public function simpan(array $transaksi): void;

    public function perbarui(string $nomor, array $perubahan): void;
}
