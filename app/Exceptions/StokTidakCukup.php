<?php

declare(strict_types=1);

namespace App\Exceptions;

final class StokTidakCukup extends KesalahanPos
{
    public function __construct(
        string $sku,
        int $diminta,
        int $tersedia,
    ) {
        parent::__construct(
            'Stok tidak cukup',
            [
                'sku' => $sku,
                'diminta' => $diminta,
                'tersedia' => $tersedia,
            ],
        );
    }

    public function kodeHttp(): int
    {
        return 422;
    }

    public function kodeKesalahan(): string
    {
        return 'stok_tidak_cukup';
    }
}
