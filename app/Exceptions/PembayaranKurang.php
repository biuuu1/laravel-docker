<?php

declare(strict_types=1);

namespace App\Exceptions;

final class PembayaranKurang extends KesalahanPos
{
    public function __construct(int $kurang)
    {
        parent::__construct(
            'Pembayaran kurang',
            ['kurang' => $kurang],
        );
    }

    public function kodeHttp(): int
    {
        return 422;
    }

    public function kodeKesalahan(): string
    {
        return 'pembayaran_kurang';
    }
}
