<?php

declare(strict_types=1);

namespace App\Domain;

enum Kategori: string
{
    case Makanan = 'makanan';
    case Minuman = 'minuman';
    case KebutuhanRumah = 'kebutuhan_rumah';
    case AlatTulis = 'alat_tulis';

    public function label(): string
    {
        return match ($this) {
            self::Makanan => 'Makanan',
            self::Minuman => 'Minuman',
            self::KebutuhanRumah => 'Kebutuhan Rumah Tangga',
            self::AlatTulis => 'Alat Tulis',
        };
    }

    public static function nilai(): array
    {
        return array_column(self::cases(), 'value');
    }
}
