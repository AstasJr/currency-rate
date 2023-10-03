<?php

namespace App\Services;

use App\Repositories\CurrencyRateRepository;
use Carbon\Carbon;

class CurrencyRateService
{
    public function __construct(private readonly CurrencyRateRepository $repository) { }

    /**
     * @throws \Exception
     */
    public function getCurrencyRateData(string $currencyCode, string $date, ?string $baseCurrencyCode = null): array
    {
        $currency = $this->repository->getCurrencyByCode($currencyCode);

        if (!$currency) {
            throw new \Exception('Валюта не найдена');
        }

        $previousDate = Carbon::parse($date)->subDay()->format('Y-m-d');

        $rate = $this->repository->getRateByCurrencyAndDate($currency->id, $date);
        $previousRate = $this->repository->getRateByCurrencyAndDate($currency->id, $previousDate);

        $diff = $rate->rate - $previousRate->rate;
        $rate->diff = round($diff, 4);

        $baseCurrencyRate = 1;

        if ($baseCurrencyCode && $baseCurrencyCode !== 'RUR') {
            $baseCurrency = $this->repository->getCurrencyByCode($baseCurrencyCode);

            if (!$baseCurrency) {
                throw new \Exception('Базовая валюта не найдена');
            }

            $baseRate = $this->repository->getRateByCurrencyAndDate($baseCurrency->id, $date);
            $baseCurrencyRate = $baseRate->rate;
        }

        $relativeRate = $rate->rate / $baseCurrencyRate;
        $relativeDiff = $rate->diff / $baseCurrencyRate;

        return [
            'value' => round(floatval($relativeRate), 4),
            'diff' => round($relativeDiff, 4),
        ];
    }
}
