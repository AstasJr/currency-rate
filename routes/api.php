<?php

use App\Http\Controllers\CurrencyRateController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CurrencyController;

Route::get('/currencies', [CurrencyController::class, 'index']);
Route::get('/currencyRate', [CurrencyRateController::class, 'index']);
