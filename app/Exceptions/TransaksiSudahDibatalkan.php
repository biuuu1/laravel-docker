<?php

declare(strict_types=1);

namespace App\Exceptions;

final class TransaksiSudahDibatalkan extends KesalahanPos
{
    public function __construct(string $nomor)
    {
        parent::__construct(
            'Transaksi sudah dibatalkan',
            ['nomor' => $nomor],
        );
    }

    public function kodeHttp(): int
    {
        return 409;
    }

    public function kodeKesalahan(): string
    {
        return 'transaksi_sudah_dibatalkan';
    }
}
