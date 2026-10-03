<?php

declare(strict_types=1);

namespace App\Domain;

enum MetodeBayar: string
{
    case Tunai = 'tunai';
    case Qris = 'qris';
    case KartuDebit = 'kartu_debit';

    public function label(): string
    {
        return match ($this) {
            self::Tunai => 'Tunai',
            self::Qris => 'QRIS',
            self::KartuDebit => 'Kartu Debit',
        };
    }

    public function butuhKembalian(): bool
    {
        return $this === self::Tunai;
    }
}
