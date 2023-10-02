<?php

namespace App\Repositories;

use App\Models\Currency;

interface CurrencyRateRepositoryInterface
{
    public function getRateByCurrencyAndDate($currencyId, $date): ?object;

    public function getCurrencyByCode($code): ?Currency;
}
