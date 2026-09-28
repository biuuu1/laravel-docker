<?php

declare(strict_types=1);

namespace App\Exceptions;

final class TransaksiTidakDitemukan extends KesalahanPos
{
    public function __construct(string $nomor)
    {
        parent::__construct(
            'Transaksi tidak ditemukan',
            ['nomor' => $nomor],
        );
    }

    public function kodeHttp(): int
    {
        return 404;
    }

    public function kodeKesalahan(): string
    {
        return 'transaksi_tidak_ditemukan';
    }
}
