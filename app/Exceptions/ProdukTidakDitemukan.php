<?php

declare(strict_types=1);

namespace App\Exceptions;

final class ProdukTidakDitemukan extends KesalahanPos
{
    public function __construct(string $sku)
    {
        parent::__construct(
            'Produk tidak ditemukan',
            ['sku' => $sku],
        );
    }

    public function kodeHttp(): int
    {
        return 404;
    }

    public function kodeKesalahan(): string
    {
        return 'produk_tidak_ditemukan';
    }
}
