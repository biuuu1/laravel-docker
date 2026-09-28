<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Contracts\RepositoriProduk;

final class RepositoriProdukArray implements RepositoriProduk
{
    private const DATA = [
        1 => ['nama' => 'Buku Tulis', 'harga' => 8000, 'stok' => 120],
        2 => ['nama' => 'Pena Gel', 'harga' => 5000, 'stok' => 300],
        3 => ['nama' => 'Tas Ransel', 'harga' => 185000, 'stok' => 12],
    ];

    public function semua(): array
    {
        return array_map(
            static fn (int $id): array => [
                'id' => $id,
                ...self::DATA[$id],
            ],
            array_keys(self::DATA),
        );
    }

    public function cari(int $id): ?array
    {
        if (!isset(self::DATA[$id])) {
            return null;
        }

        return [
            'id' => $id,
            ...self::DATA[$id],
        ];
    }
}
