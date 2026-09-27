<?php

declare(strict_types=1);

namespace App\Domain;

final class Uang
{
    public function __construct(
        public readonly int $jumlah,
        public readonly string $mataUang = 'IDR',
    ) {
        if ($jumlah < 0) {
            throw new \InvalidArgumentException('Jumlah uang tidak boleh negatif');
        }
    }

    public function tambah(self $lain): self
    {
        $this->pastikanMataUangSama($lain);

        // Kembalikan objek BARU, jangan ubah yang lama (immutable)
        return new self($this->jumlah + $lain->jumlah, $this->mataUang);
    }

    public function kali(int $pengali): self
    {
        return new self($this->jumlah * $pengali, $this->mataUang);
    }

    public function format(): string
    {
        return 'Rp ' . number_format($this->jumlah, 0, ',', '.');
    }

    private function pastikanMataUangSama(self $lain): void
    {
        if ($this->mataUang !== $lain->mataUang) {
            throw new \DomainException('Mata uang berbeda tidak dapat dijumlahkan');
        }
    }
}