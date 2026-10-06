<?php

declare(strict_types=1);

namespace App\Contracts;

interface RepositoriTransaksi
{
    public function tanggal(string $tanggal): array;

    public function cariNomor(string $nomor): ?array;

    public function simpan(array $transaksi): void;

    public function perbarui(string $nomor, array $perubahan): void;

    public function urutanBerikutnya(string $tanggal): int;
}
