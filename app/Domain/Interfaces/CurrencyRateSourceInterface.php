<?php

namespace App\Domain\Interfaces;

use App\Domain\Entities\CurrenciesCollection;
use Carbon\CarbonImmutable;

interface CurrencyRateSourceInterface
{
    /**
     * Получить значения курсов валют
     *
     * @param CarbonImmutable $date
     * @return CurrenciesCollection
     */
    public function getData(CarbonImmutable $date): CurrenciesCollection;
}
