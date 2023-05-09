<?php

namespace App\Domain\Services;

use App\Domain\Entities\Currency;
use App\Domain\Entities\CurrencyRate;
use App\Domain\Interfaces\CurrencyRateSourceInterface;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Redis;

final class CurrencyRateService
{
    public function __construct(
        private readonly CurrencyRateSourceInterface $currencyRateSource
    )
    {
        //
    }

    /**
     * Получить курс валют по указанной паре тикеров
     *
     * @param string $needleCurrency
     * @param string $baseCurrency
     * @param CarbonImmutable|null $date
     * @return CurrencyRate
     */
    public function getCurrencyRate(string $needleCurrency, string $baseCurrency = 'RUR', CarbonImmutable $date = null): CurrencyRate
    {
        $needleCurrency = strtoupper($needleCurrency);
        $baseCurrency = strtoupper($baseCurrency);

        // Если не указана конкретная дата, подставить сегодняшний день
        if (is_null($date)) {
            $date = CarbonImmutable::today()->format('d/m/Y');
        }

        // Если указана одна и та же валюта в паре
        if ($needleCurrency === $baseCurrency) {
            throw new \DomainException('Вы указали одну и ту же валюту в паре. Укажите разные валюты', 400);
        }

        // Проверить дату, на случай запроса даты из будущего
        if ( $date->startOfDay()->gt(CarbonImmutable::today()->startOfDay()) ) {
            throw new \DomainException('Можно указывать только дату до текущего дня', 400);
        }

        /** @var Currency|null $needle Объект требуемой валюты */
        $needle = null;

        /** @var Currency|null $base Объект требуемой валюты */
        $base = null;

        // Вычисленное значение для валютной пары на конкретную дату из кэша
        $currencyRateValueFromCache = Redis::get("{$needleCurrency}:{$baseCurrency}:{$date->toDateString()}");

        if ($currencyRateValueFromCache) {
            return new CurrencyRate($needleCurrency, $baseCurrency, $currencyRateValueFromCache);
        }

        // Проверить данные в базе данных
        $currencyRateValueFromDb = DB::table('currencies_rate_history')
            ->selectRaw('rate AS needle')
            ->selectSub(
                DB::table('currencies_rate_history')
                    ->select('rate')
                    ->where('char_code', '=', $baseCurrency)
                    ->where('date', '=', $date->toDateString()), 'base')
            ->where('char_code', '=', $needleCurrency)
            ->where('date', '=', $date->toDateString())
            ->first();

        // Если есть запись в БД по запрашиваемой валютной паре
        if ($currencyRateValueFromDb) {

            // Если присутствуют данные по обеим валютам
            if ($currencyRateValueFromDb->needle && $currencyRateValueFromDb->base) {

                if ($baseCurrency === 'RUR') {
                    return new CurrencyRate($needleCurrency, $baseCurrency, $currencyRateValueFromDb->needle);
                }
                else {
                    return new CurrencyRate($needleCurrency, $baseCurrency, ( ($currencyRateValueFromDb->needle * 100) / $currencyRateValueFromDb->base ) / 100);
                }
            }
        }

        // Получить данные с сайта ЦБ
        $currenciesCollection = $this->currencyRateSource->getData($date);

        foreach ($currenciesCollection->currencies as $currency) {
            if ($currency->charCode === $needleCurrency) {
                $needle = $currency;
            }

            if ($currency->charCode === $baseCurrency) {
                $base = $currency;
            }
        }

        if ( is_null($needle) ) {
            throw new \DomainException("Валюта $needleCurrency не найдена или ее не существует в заданную дату");
        }

        if ( is_null($base) && $baseCurrency !== 'RUR') {
            throw new \DomainException("Валюта $baseCurrency не найдена или ее не существует в заданную дату");
        }

        // Курс требуемой валюты, по отношению к базовой валюте
        if ( $base ) {
            $rate = ( ($needle->value * 100) / $base->value ) / 100;
        }
        // Если базовая валюта не указана, значит требуется курс по отношению к рублю
        else {
            $rate = $needle->value;
        }

        Redis::set("{$needleCurrency}:{$baseCurrency}:{$date->toDateString()}", $rate);

        return new CurrencyRate($needleCurrency, $baseCurrency, $rate);
    }
}
