<?php

declare(strict_types=1);

namespace App\Domain;

enum MetodeBayar: string
{
    case Tunai = 'tunai';
    case Qris = 'qris';
    case KartuDebit = 'kartu_debit';

    public function butuhKembalian(): bool
    {
        return $this === self::Tunai;
    }
}
