<?php

namespace App\Repositories;

use App\Models\OrderTransaction;

class OrderTransactionRepository
{
    public function createOrder($data)
    {
        $orderTransaction = OrderTransaction::create($data);

        return $orderTransaction;
    }

    public function getTransactionsAmount($order)
    {
        return OrderTransaction::where(function ($q) use ($order) {
            $q->where('buy_order_id', $order->id)
                ->orWhere('sell_order_id', $order->id);
        })->sum('amount');
    }
}
