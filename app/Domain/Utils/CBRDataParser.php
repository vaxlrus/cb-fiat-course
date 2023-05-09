<?php

namespace App\Domain\Utils;

use App\Domain\Entities\CurrenciesCollection;
use App\Domain\Entities\Currency;
use App\Domain\Entities\Tickers;

final class CBRDataParser
{
    /**
     * Распарсить XML полученный с сайта cbr.ru
     *
     * @param string $xml
     * @return CurrenciesCollection
     */
    public static function parse(string $xml): CurrenciesCollection
    {
        $xml = simplexml_load_string($xml);

        $currencies = [];

        foreach ($xml->Valute as $valute) {
            $currencies[] = new Currency(
                (int) $valute->NumCode,
                $valute->CharCode,
                (int) $valute->Nominal,
                (string) $valute->Name,
                (float) str_replace(',', '.', strval($valute->Value))
            );
        }

        unset($valute);

        return new CurrenciesCollection($currencies);
    }
}
