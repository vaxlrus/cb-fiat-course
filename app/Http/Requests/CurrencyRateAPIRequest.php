<?php

namespace App\Http\Requests;

use Carbon\CarbonImmutable;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

final class CurrencyRateAPIRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'needle_currency' => ['required', 'string', 'min:3', 'max:3'],
            'base_currency' => ['string', 'min:3', 'max:3'],
            'date' => ['string', 'max:10']
        ];
    }

    public function messages(): array
    {
        return [
            'needle_currency.required' => 'Не указан код валюты',
            'needle_currency.string' => 'Код валюты должен быть строкой',
            'needle_currency.min' => 'Код валюты должен содержать 3 символа',
            'needle_currency.max' => 'Код валюты должен содержать 3 символа',

            'base_currency.string' => 'Код валюты должен быть строкой',
            'base_currency.min' => 'Код валюты должен содержать 3 символа',
            'base_currency.max' => 'Код валюты должен содержать 3 символа',

            'date.string' => 'Дата должна быть строковым значением вида: dd/mm/yyyy',
            'date.max' => 'Дата не должна быть более 10 символов. Укажите дату в виде: dd/mm/yyyy'
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            response()->json([
                'message' => 'Ошибка валидации',
                'data' => $validator->errors()
            ], 400)
        );
    }

    /**
     * Заполнить не обязательные аргументы, данными по умолчанию
     *
     * @return void
     */
    protected function prepareForValidation(): void
    {
        if (! $this->query->has('base_currency')) {
            $this->merge([
                'base_currency' => 'RUR',
            ]);
        }

        if (! $this->query->has('date')) {
            $this->merge([
                'date' => CarbonImmutable::today()->startOfDay()->format('d/m/Y')
            ]);
        }
        else {
            $this->query->set(
                'date',
                str_replace(['-','.',','], '/', $this->query->get('date'))
            );
        }
    }
}
