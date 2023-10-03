<?php

namespace App\Http\Controllers;

use App\Http\Requests\CurrencyRateRequest;
use App\Repositories\CurrencyRateRepositoryInterface;
use App\Services\CurrencyRateService;
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
    public function index(CurrencyRateRequest $request, CurrencyRateService $service)
    {
        try {
            $currencyCode = $request->input('currencyCode');
            $date = $request->input('date');
            $baseCurrencyCode = $request->input('baseCurrencyCode');

            $result = $service->getCurrencyRateData($currencyCode, $date, $baseCurrencyCode);

            return response()->json($result);
        } catch (\PDOException $e) {
            return response()->json(['error' => 'Ошибка базы данных: ' . $e->getMessage()], 500);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
