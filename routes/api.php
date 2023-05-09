<?php

use App\Http\Controllers\CurrencyRateController;
use Illuminate\Support\Facades\Route;

Route::get('/exchange-rate', [CurrencyRateController::class, 'getCurrencyRate']);
