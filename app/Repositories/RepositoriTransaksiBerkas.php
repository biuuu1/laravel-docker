<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Contracts\RepositoriTransaksi;

final class RepositoriTransaksiBerkas implements RepositoriTransaksi
{
    private string $path;

    public function __construct()
    {
        $this->path = storage_path('app/transaksi.json');
    }

    public function semua(): array
    {
        if (!is_file($this->path)) {
            return [];
        }

        $isi = file_get_contents($this->path);

        if ($isi === false || trim($isi) === '') {
            return [];
        }

        $data = json_decode($isi, true);

        return is_array($data) ? $data : [];
    }

    public function cari(string $nomor): ?array
    {
        foreach ($this->semua() as $transaksi) {
            if (($transaksi['nomor'] ?? null) === $nomor) {
                return $transaksi;
            }
        }

        return null;
    }

    public function simpan(array $transaksi): array
    {
        $data = $this->semua();
        $data[] = $transaksi;

        $this->tulis($data);

        return $transaksi;
    }

    public function perbarui(string $nomor, array $transaksi): array
    {
        $data = $this->semua();

        foreach ($data as $index => $item) {
            if (($item['nomor'] ?? null) === $nomor) {
                $data[$index] = $transaksi;
                $this->tulis($data);

                return $transaksi;
            }
        }

        return $transaksi;
    }

    private function tulis(array $data): void
    {
        $direktori = dirname($this->path);

        if (!is_dir($direktori)) {
            mkdir($direktori, 0775, true);
        }

        file_put_contents(
            $this->path,
            json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE),
        );
    }
}
