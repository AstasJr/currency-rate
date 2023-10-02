<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CurrencyRateRequest extends FormRequest
{
    public function rules()
    {
        return [
            'date' => ['required', 'date_format:Y-m-d'],
            'currencyCode' => ['required', 'string', 'max:10'],
            'baseCurrencyCode' => ['sometimes', 'string', 'max:10'],
        ];
    }
}
