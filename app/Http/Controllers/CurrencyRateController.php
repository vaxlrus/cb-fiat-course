<?php

namespace App\Http\Controllers;

use App\Domain\Exceptions\InvalidTickerException;
use App\Domain\Exceptions\UnprocessableDateException;
use App\Domain\Services\CurrencyRateService;
use App\Http\Requests\CurrencyRateAPIRequest;
use Carbon\CarbonImmutable;
use Illuminate\Http\JsonResponse;

final class CurrencyRateController extends Controller
{
    public function __construct(private readonly CurrencyRateService $currencyRateService)
    {
        //
    }

    /**
     * Получение курса валюты с сайта ЦБ
     *
     * @param CurrencyRateAPIRequest $request
     * @return JsonResponse
     */
    public function getCurrencyRate(CurrencyRateAPIRequest $request): JsonResponse
    {
        $needleCurrency = $request->get('needle_currency');
        $baseCurrency = $request->get('base_currency');

        $date = CarbonImmutable::createFromFormat('d/m/Y', $request->get('date'));

        // Получить данные по указанной валютной паре с сайта ЦБ
        try {
            $todayCurrencyRate = $this->currencyRateService->getCurrencyRate(
                $needleCurrency,
                $baseCurrency,
                $date
            );

            $yesterdayCurrencyRate = $this->currencyRateService->getCurrencyRate(
                $needleCurrency,
                $baseCurrency,
                $date->subDay()
            );
        }
        catch (UnprocessableDateException $e) {
            return new JsonResponse([
                'message' => 'Ошибка',
                'data' => 'На указанную дату не существует курса валют'
            ], 400);
        }
        catch (\DomainException $e) {
            return new JsonResponse([
                'message' => $e->getMessage()
            ], ( $e->getCode() === 0 ? 400 : $e->getCode()) );
        }
        catch (InvalidTickerException $e) {
            return new JsonResponse([
                'message' => 'Неверный код валюты',
                'data' => $e->getMessage()
            ], 400);
        }

        return new JsonResponse([
            'message' => 'Курс валюты',
            'data' => [
                'today' => [
                    'value' => round($todayCurrencyRate->rate, 4),
                    'ticker' => $todayCurrencyRate->baseCurrency
                ],
                'yesterday' => [
                    'value' => round($yesterdayCurrencyRate->rate, 4),
                    'ticker' => $yesterdayCurrencyRate->baseCurrency
                ],
                'difference' => round($todayCurrencyRate->rate - $yesterdayCurrencyRate->rate, 4)
            ]
        ], 200);
    }
}
