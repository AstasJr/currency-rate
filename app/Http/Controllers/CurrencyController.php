<?php

namespace App\Http\Controllers;

use App\Models\Currency;

class CurrencyController extends Controller
{
    /**
     * @OA\Get(
     *      path="/api/currencies",
     *      operationId="getCurrenciesList",
     *      tags={"Currencies"},
     *      summary="Список валют",
     *      description="Получить список валют с кодами и названиями",
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(
     *              type="array",
     *              @OA\Items(
     *                  type="object",
     *                  @OA\Property(property="id", type="string", description="Currency ID", example="R01010"),
     *                  @OA\Property(property="code", type="string", description="Currency Code", example="AUD"),
     *                  @OA\Property(property="name", type="string", description="Currency Name", example="Австралийский доллар")
     *              )
     *          )
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="No currencies found"
     *      ),
     *      @OA\Response(
     *          response=500,
     *          description="Server error"
     *      )
     * )
     */
    public function index()
    {
        try {
            $currencies = Currency::all();

            if (!$currencies->count()) {
                return response()->json(['error' => 'Валюты не найдены'], 404);
            }

            return response()->json($currencies);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Произошла внутренняя ошибка: ' . $e->getMessage()], 500);
        }
    }
}

