<?php

namespace App\Domain\Entities;

final class CurrenciesCollection
{
    public function __construct(public readonly array $currencies)
    {
    }
}
