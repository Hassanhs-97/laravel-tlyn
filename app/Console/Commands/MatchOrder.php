<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class MatchOrder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tlyn:match-order';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Match orders in the system';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Matching orders...');

        (new \App\Jobs\OrderMatchJob())->dispatch();

        $this->info('Orders matched successfully.');
    }
}
