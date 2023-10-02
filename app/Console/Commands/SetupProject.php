<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class SetupProject extends Command
{
    protected $signature = 'setup:project';

    protected $description = 'Setup the project by running multiple commands';

    public function handle()
    {
        $this->info('Generating app key...');
        $this->call('key:generate');

        $this->info('Running migrations...');
        $this->call('migrate');

        $this->info('Generating Swagger documentation...');
        $this->call('l5-swagger:generate');

        $this->info('Project setup completed!`');
    }
}
