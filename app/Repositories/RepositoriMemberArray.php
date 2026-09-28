<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Contracts\RepositoriMember;

final class RepositoriMemberArray implements RepositoriMember
{
    private const DATA = [
        'MBR-001' => [
            'kode' => 'MBR-001',
        ],
    ];

    public function cari(string $kode): ?array
    {
        return self::DATA[$kode] ?? null;
    }
}
