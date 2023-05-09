<?php

namespace App\Domain\ExchangeRateSources;

use App\Domain\Entities\CurrenciesCollection;
use App\Domain\Entities\Currency;
use App\Domain\Exceptions\UnprocessableDateException;
use App\Domain\Interfaces\CurrencyRateSourceInterface;
use App\Domain\Utils\CBRDataParser;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\File;

final class FakeXMLCurrencySource implements CurrencyRateSourceInterface
{
    /**
     * Получить курс валют на указанную дату
     *
     * @param CarbonImmutable $date
     * @return CurrenciesCollection
     * @throws UnprocessableDateException
     */
    public function getData(CarbonImmutable $date): CurrenciesCollection
    {
        if ($date->day === 4) {
            $xml = File::get(
                storage_path('4-may.xml')
            );
        }
        else if ($date->day === 5) {
            $xml = File::get(
                storage_path('5-may.xml')
            );
        }
        else {
            throw new UnprocessableDateException();
        }

        return CBRDataParser::parse($xml);
    }
}
