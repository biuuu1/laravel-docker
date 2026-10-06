<?php

declare(strict_types=1);

namespace App\Contracts;

interface RepositoriProduk
{
    public function semua(): array;

    public function cariSku(string $sku): ?array;
}
