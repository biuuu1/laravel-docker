<?php

declare(strict_types=1);

namespace App\Services;

use App\Domain\Uang;

final class LayananProduk
{
    /** Sumber data sementara, akan diganti Eloquent pada Modul 4 */
    private const DATA = [
        1 => ['nama' => 'Buku Tulis', 'harga' => 8000, 'stok' => 120],
        2 => ['nama' => 'Pena Gel', 'harga' => 5000, 'stok' => 300],
        3 => ['nama' => 'Tas Ransel', 'harga' => 185000, 'stok' => 12],
    ];

    public function semua(): array
    {
        return array_map(
            fn (int $id) => $this->format($id),
            array_keys(self::DATA),
        );
    }

    public function cari(int $id): ?array
    {
        return isset(self::DATA[$id]) ? $this->format($id) : null;
    }

    private function format(int $id): array
    {
        $item = self::DATA[$id];

        return [
            'id' => $id,
            'nama' => $item['nama'],
            'harga' => $item['harga'],
            'harga_format' => (new Uang($item['harga']))->format(),
            'stok' => $item['stok'],
            'tersedia' => $item['stok'] > 0,
        ];
    }
}