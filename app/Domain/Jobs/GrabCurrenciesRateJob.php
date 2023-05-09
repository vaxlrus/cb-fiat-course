<?php

namespace App\Domain\Jobs;

use App\Domain\Exceptions\CBRHostNotAvailable;
use App\Domain\Exceptions\UnexpectedDataProvided;
use App\Domain\Interfaces\CurrencyRateSourceInterface;
use Carbon\CarbonImmutable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class GrabCurrenciesRateJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public readonly CarbonImmutable $date)
    {
        //
    }

    public function handle(CurrencyRateSourceInterface $currencyRateSource): void
    {
        try {
            $currenciesRateCollection = $currencyRateSource->getData($this->date);

            foreach ($currenciesRateCollection->currencies as $currency) {
                DB::table('currencies_rate_history')->updateOrInsert(
                    [
                        'char_code' => $currency->charCode,
                        'date' => $this->date->toDateString(),
                    ],
                    [
                        'rate' => $currency->value
                    ]
                );
            }

            Log::info('Данные курсов валют за ' . $this->date->toDateString() . ' собраны');
        }
        catch (CBRHostNotAvailable $e) {
            Log::error('Сбор данных за ' . $this->date->toDateString() . ' провалился. Хост не доступен');
        }
        catch (UnexpectedDataProvided $e) {
            Log::error($e->getMessage(), [
                'response' => $e->getResponse()
            ]);
        }
    }
}
