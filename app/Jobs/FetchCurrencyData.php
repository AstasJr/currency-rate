<?php

namespace App\Jobs;

use App\Models\Currency;
use App\Models\CurrencyRate;
use Carbon\Carbon;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;

class FetchCurrencyData implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(private readonly string $date) { }

    /**
     * @throws GuzzleException
     */
    public function handle()
    {
        $client = new Client();
        $formattedDate = Carbon::createFromFormat('Y-m-d', $this->date)->format('d/m/Y');
        $response = $client->get('https://www.cbr.ru/scripts/XML_daily.asp?date_req=' . $formattedDate);
        $xml = simplexml_load_string($response->getBody()->getContents());

        foreach ($xml->Valute as $valute) {
            $id = (string)$valute['ID'];
            $code = (string) $valute->CharCode;
            $value = (string) $valute->Value;

            $currency = Currency::firstOrCreate(['id' => $id], ['code' => $code]);

            $exists = CurrencyRate::where('currency_id', $currency->id)
                ->where('date', $this->date)
                ->where('rate', str_replace(',', '.', $value))
                ->where('base_currency_code', 'RUR')
                ->exists();

            if (!$exists) {
                CurrencyRate::create([
                    'currency_id' => $currency->id,
                    'date' => $this->date,
                    'rate' => str_replace(',', '.', $value),
                    'base_currency_code' => 'RUR'
                ]);
            }

            $cacheKey = "currency_rate_{$currency->id}_{$this->date}";
            $cacheData = [
                'currency_id' => $currency->id,
                'date' => $this->date,
                'rate' => str_replace(',', '.', $value),
                'base_currency_code' => 'RUR'
            ];
            Cache::forever($cacheKey, $cacheData);
        }
    }
}
