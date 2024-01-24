<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class AppRefresh extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:refresh';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clears the database, runs migrations, and seeds the database.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->line('Refreshing application...');

        $this->info('Generating application key...');
        Artisan::call('key:generate');

        $this->info('Clearing database...');
        Artisan::call('migrate:reset');

        $this->info('Running migrations...');
        Artisan::call('migrate');

        $this->info('Seeding database...');
        Artisan::call('db:seed');

        $this->info('Application refreshed and ready to use!');
    }
}
