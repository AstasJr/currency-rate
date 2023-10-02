<?php

namespace App\Repositories;

use Illuminate\Support\Facades\Cache;
use App\Models\CurrencyRate;
use App\Models\Currency;

class CurrencyRateRepository implements CurrencyRateRepositoryInterface
{
    public function getRateByCurrencyAndDate($currencyId, $date): ?object
    {
        $cacheKey = "currency_rate_{$currencyId}_{$date}";
        if (Cache::has($cacheKey)) {
            return (object)Cache::get($cacheKey);
        } else {
            return CurrencyRate::where('currency_id', $currencyId)
                ->where('date', $date)
                ->first();
        }
    }

    public function getCurrencyByCode($code): ?Currency
    {
        return Currency::where('code', $code)->first();
    }
}
