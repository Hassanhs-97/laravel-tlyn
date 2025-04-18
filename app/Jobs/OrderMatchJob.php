<?php

namespace App\Jobs;

use App\Repositories\OrderRepository;
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
        (new OrderRepository(new OrderTransactionRepository))->matchOrders();
    }
}
