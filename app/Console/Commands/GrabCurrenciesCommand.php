<?php

namespace App\Console\Commands;

use App\Domain\Jobs\GrabCurrenciesRateJob;
use Carbon\CarbonImmutable;
use Illuminate\Console\Command;

class GrabCurrenciesCommand extends Command
{
    protected $signature = 'app:grab-currencies {--days=180}';

    protected $description = 'Собрать информацию с сайта cbr.ru на указанное количество дней назад от текущего дня';

    public function handle()
    {
        $daysCount = (int) $this->option('days');

        $today = CarbonImmutable::today()->startOfDay();

        for ($i = 0; $i <= $daysCount; $i++) {
            GrabCurrenciesRateJob::dispatch(
                $today->subDays($i)
            );
        }

        $this->info('Запущен процесс сбора данных');
    }
}
