<?php

namespace App\Domain\Entities;

final class CurrencyRate
{
    public function __construct(
        public readonly string $needleCurrency,
        public readonly string $baseCurrency,
        public readonly float $rate
    )
    {
        //
    }
}
