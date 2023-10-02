<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Jobs\FetchAndStoreCurrencies;

class FetchCurrencies extends Command
{
    protected $signature = 'fetch:currencies';
    protected $description = 'Fetch currencies from cbr.ru and store them';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $this->info('Start fetching currencies from cbr.ru');
        dispatch(new FetchAndStoreCurrencies());
        $this->info('Finish fetching currencies from cbr.ru');
    }
}
