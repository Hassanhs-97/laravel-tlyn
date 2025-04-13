<?php

namespace App\Jobs;

use App\Models\OrderTransaction;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class UpdateUserGoldBalance implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(public OrderTransaction $transaction) {
        $this->onQueue('goldBalance');
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $transaction = $this->transaction;

        $buyer = $transaction->buyOrder->user;
        $buyer->increment('gold_balance', $transaction->amount);

        $seller = $transaction->sellOrder->user;
        $seller->decrement('gold_balance', $transaction->amount);
    }
}
