<?php

declare(strict_types=1);

namespace App\Exceptions;

use RuntimeException;

abstract class KesalahanPos extends RuntimeException
{
    public function __construct(
        string $message,
        private readonly array $konteksData = [],
    ) {
        parent::__construct($message);
    }

    abstract public function kodeHttp(): int;

    abstract public function kodeKesalahan(): string;

    public function konteks(): array
    {
        return $this->konteksData;
    }
}
