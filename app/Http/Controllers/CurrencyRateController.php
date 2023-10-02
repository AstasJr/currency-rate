<?php

namespace App\Http\Controllers;

use App\Http\Requests\CurrencyRateRequest;
use App\Repositories\CurrencyRateRepositoryInterface;
use Carbon\Carbon;

class CurrencyRateController extends Controller
{
    public function __construct(private readonly CurrencyRateRepositoryInterface $repository) {}

    /**
     * @OA\Get(
     *      path="/api/currencyRate",
     *      operationId="getCurrencyRate",
     *      tags={"CurrencyRate"},
     *      summary="Курс валюты",
     *      description="Получить курс валюты за определенную дату",
     *      @OA\Parameter(
     *          name="currencyCode",
     *          description="Код валюты. Можно получить в методе /api/currencies",
     *          required=true,
     *          in="query",
     *          @OA\Schema(
     *              type="string",
     *              example="USD"
     *          )
     *      ),
     *     @OA\Parameter(
     *          name="date",
     *          in="query",
     *          description="Дата за которую надо получить курс валюты, максимум 179 дней назад",
     *          required=true,
     *          @OA\Schema(
     *              type="string",
     *              format="date",
     *              example="2023-09-29"
     *          )
     *      ),
     *      @OA\Parameter(
     *          name="baseCurrencyCode",
     *          description="Базовый код валюты, по умолчанию RUR",
     *          required=false,
     *          in="query",
     *          @OA\Schema(
     *              type="string",
     *              example="EUR"
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="value", type="number", example=0.9501, description="The currency rate value"),
     *              @OA\Property(property="diff", type="number", example=0.0049, description="The difference from the previous rate")
     *          )
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Not Found",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="error", type="string", example="Валюта не найдена")
     *          )
     *      ),
     *      @OA\Response(
     *          response=500,
     *          description="Internal Server Error",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="error", type="string", example="Произошла внутренняя ошибка")
     *          )
     *      )
     * )
     */
    public function index(CurrencyRateRequest $request)
    {
        try {
            $currencyCode = $request->input('currencyCode');
            $currency = $this->repository->getCurrencyByCode($currencyCode);

            if (!$currency) {
                return response()->json(['error' => 'Валюта не найдена'], 404);
            }

            $date = $request->input('date');
            $previousDate = Carbon::parse($date)->subDay()->format('Y-m-d');

            if (!$rate = $this->repository->getRateByCurrencyAndDate($currency->id, $date)) {
                return response()->json(['error' => 'Данных по запрашиваемой дате не найдено'], 404);
            }
            if (!$previousRate = $this->repository->getRateByCurrencyAndDate($currency->id, $previousDate)) {
                return response()->json(['error' => 'Данных за предыдущий день не найдено'], 404);
            }

            $diff = $rate->rate - $previousRate->rate;
            $rate->diff = round($diff, 4);

            $baseCurrencyRate = 1;
            if ($request->has('baseCurrencyCode') && $request->input('baseCurrencyCode') !== 'RUR') {
                $baseCurrencyCode = $request->input('baseCurrencyCode');
                $baseCurrency = $this->repository->getCurrencyByCode($baseCurrencyCode);

                if (!$baseCurrency) {
                    return response()->json(['error' => 'Базовая валюта не найдена'], 404);
                }

                $baseRate = $this->repository->getRateByCurrencyAndDate($baseCurrency->id, $date);
                $baseCurrencyRate = $baseRate->rate;
            }

            $relativeRate = $rate->rate / $baseCurrencyRate;
            $relativeDiff = $rate->diff / $baseCurrencyRate;

            $result = [
                'value' => round(floatval($relativeRate), 4),
                'diff' => round($relativeDiff, 4),
            ];

            return response()->json($result);
        } catch (\PDOException $e) {
            return response()->json(['error' => 'Ошибка базы данных: ' . $e->getMessage()], 500);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Произошла внутренняя ошибка: ' . $e->getMessage()], 500);
        }
    }
}
