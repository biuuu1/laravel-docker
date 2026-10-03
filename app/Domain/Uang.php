<?php

declare(strict_types=1);

namespace App\Domain;

use InvalidArgumentException;

final readonly class Uang
{
    public function __construct(public int $rupiah)
    {
        if ($rupiah < 0) {
            throw new InvalidArgumentException('Nilai uang tidak boleh negatif.');
        }
    }

    public static function nol(): self
    {
        return new self(0);
    }

    public function tambah(self $lain): self
    {
        return new self($this->rupiah + $lain->rupiah);
    }

    public function kurang(self $lain): self
    {
        return new self($this->rupiah - $lain->rupiah);
    }

    public function kali(int $faktor): self
    {
        return new self($this->rupiah * $faktor);
    }

    public function persen(float $persen): self
    {
        return new self((int) round($this->rupiah * $persen / 100));
    }

    public function bulatkanKeAtas(int $kelipatan): self
    {
        return new self(
            (int) (ceil($this->rupiah / $kelipatan) * $kelipatan)
        );
    }

    public function kurangDari(self $lain): bool
    {
        return $this->rupiah < $lain->rupiah;
    }

    public function format(): string
    {
        return 'Rp '.number_format($this->rupiah, 0, ',', '.');
    }
}
