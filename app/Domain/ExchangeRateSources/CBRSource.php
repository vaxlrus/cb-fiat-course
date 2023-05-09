<?php

namespace App\Domain\ExchangeRateSources;

use App\Domain\Entities\CurrenciesCollection;
use App\Domain\Exceptions\CBRHostNotAvailable;
use App\Domain\Exceptions\UnexpectedDataProvided;
use App\Domain\Interfaces\CurrencyRateSourceInterface;
use App\Domain\Utils\CBRDataParser;
use Carbon\CarbonImmutable;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;

final class CBRSource implements CurrencyRateSourceInterface
{
    private const URL = "http://www.cbr.ru/scripts/XML_daily.asp";
    private const USER_AGENT = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/112.0.0.0 Safari/537.36 Edg/112.0.1722.68';

    /**
     * Получение курса валюты с сайта Центробанка
     *
     * @param CarbonImmutable $date
     * @return CurrenciesCollection
     * @throws CBRHostNotAvailable
     * @throws UnexpectedDataProvided
     */
    public function getData(CarbonImmutable $date): CurrenciesCollection
    {
        try {
            $response = Http::withHeaders(['User-Agent' => self::USER_AGENT])
                ->timeout(2)
                ->connectTimeout(2)
                ->get(self::URL, ['date_req' => $date->format('d/m/Y')]);
        }
        catch (ConnectionException $e) {
            throw new CBRHostNotAvailable();
        }

        if ($response->failed()) {
            throw new UnexpectedDataProvided('Ошибка при получении данных с сайта cbr.ru', $response);
        }

        return CBRDataParser::parse($response->body());
    }
}
