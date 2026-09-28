<?php

declare(strict_types=1);

namespace App\Exceptions;

final class MemberTidakDitemukan extends KesalahanPos
{
    public function __construct(string $kode)
    {
        parent::__construct(
            'Member tidak ditemukan',
            ['kode' => $kode],
        );
    }

    public function kodeHttp(): int
    {
        return 404;
    }

    public function kodeKesalahan(): string
    {
        return 'kode_member_tidak_ditemukan';
    }
}
