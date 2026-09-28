<?php

declare(strict_types=1);

namespace App\Services;

use App\Contracts\RepositoriProduk;

final class LayananKatalog
{
    public function __construct(
        private RepositoriProduk $produk,
    ) {
    }

    public function semua(): array
    {
        return $this->produk->semua();
    }

    public function cari(int $id): ?array
    {
        return $this->produk->cari($id);
    }
}
