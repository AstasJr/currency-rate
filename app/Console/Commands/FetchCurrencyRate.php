<?php

namespace App\Console\Commands;

use App\Jobs\FetchCurrencyData;
use Illuminate\Console\Command;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class FetchCurrencyRate extends Command
{
    protected $signature = 'fetch:currency-rate {days=180 : Number of days to fetch}';

    protected $description = 'Fetch currency rates';

    public function handle(): void
    {
        $this->info('Start fetching currency rate');

        try {
            $days = $this->argument('days');
            for ($i = 0; $i < $days; $i++) {
                $date = Carbon::now()->subDays($i)->format('Y-m-d');
                FetchCurrencyData::dispatch($date);
            }
            $this->info('Finish fetching currency rate');
        } catch (\Exception $e) {
            $this->error("Произошла ошибка: " . $e->getMessage());
            Log::error($e->getMessage(), ['exception' => $e]);
        }
    }
}
