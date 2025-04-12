<?php

namespace App\Jobs;

use App\Repositories\OrderRepositoey;
use App\Repositories\OrderTransactionRepository;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class OrderMatchJob implements ShouldQueue
{
    use Queueable;
    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        $this->onQueue('orderMatch');
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        (new OrderRepositoey(new OrderTransactionRepository))->matchOrders();
    }
}
