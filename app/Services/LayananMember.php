<?php

declare(strict_types=1);

namespace App\Services;

use App\Contracts\RepositoriMember;

final class LayananMember
{
    public function __construct(
        private RepositoriMember $member,
    ) {}

    public function cari(string $kode): ?array
    {
        return $this->member->cari($kode);
    }

    public function terdaftar(string $kode): bool
    {
        return $this->member->cari($kode) !== null;
    }
}
