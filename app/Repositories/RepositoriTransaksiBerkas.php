<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Contracts\RepositoriTransaksi;
use Illuminate\Support\Facades\Storage;

final class RepositoriTransaksiBerkas implements RepositoriTransaksi
{
    private const BERKAS = 'pos/transaksi.json';

    public function semua(): array
    {
        if (! Storage::disk('local')->exists(self::BERKAS)) {
            return [];
        }

        $isi = Storage::disk('local')->get(self::BERKAS);

        return json_decode($isi ?? '[]', true, 512, JSON_THROW_ON_ERROR) ?: [];
    }

    public function cariNomor(string $nomor): ?array
    {
        return $this->semua()[$nomor] ?? null;
    }

    public function simpan(array $transaksi): void
    {
        $semua = $this->semua();
        $semua[$transaksi['nomor']] = $transaksi;

        $this->tulis($semua);
    }

    public function perbarui(string $nomor, array $perubahan): void
    {
        $semua = $this->semua();

        if (! isset($semua[$nomor])) {
            return;
        }

        $semua[$nomor] = array_merge($semua[$nomor], $perubahan);

        $this->tulis($semua);
    }

    private function tulis(array $data): void
    {
        Storage::disk('local')->put(
            self::BERKAS,
            json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE),
        );
    }
}
