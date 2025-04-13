<?php

namespace App\Repositories;

use App\Models\Order;
use App\Services\Fee\FeeCalculatorInterface;
use App\Services\Fee\FeeService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OrderRepository
{
    public function __construct(public OrderTransactionRepository $orderTransactionRepository) {}

    public function getAllOrders()
    {
        return Order::where('user_id', Auth::id())
            ->latest()
            ->paginate(20);
    }

    public function getOrderById($id)
    {
        $order = Order::lockforUpdate()->find($id);

        if ($order) {
            return $order;
        }

        return null;
    }

    public function createOrder($data)
    {
        $data['user_id'] = Auth::id();
        $data['status']  = Order::STATUS_OPEN;
        $order           = Order::create($data);

        return $order;
    }

    public function updateOrder($id, $data)
    {
        $order = $this->getOrderById($id);

        if ($order) {
            if ($order->status == Order::STATUS_OPEN) {
                $order->update($data);
            }

            return $order;
        }

        return null;
    }

    public function matchOrders()
    {
        Order::whereIn('status', [Order::STATUS_OPEN, Order::STATUS_PARTIAL])
            ->orderBy('created_at')
            ->chunk(10, function ($orders) {
                foreach ($orders as $order) {
                    DB::transaction(function () use ($order) {
                        // Lock current order
                        $order = Order::where('id', $order->id)->lockForUpdate()->first();

                        if (!$order) {
                            return;
                        }

                        if ($order->status === Order::STATUS_COMPLETED) {
                            return;
                        }

                        $matchedAmount = $this->orderTransactionRepository
                            ->getTransactionsAmount($order);

                        $remaining = $order->amount - $matchedAmount;

                        if ($remaining <= 0) {
                            $this->updateOrderStatus($order, Order::STATUS_COMPLETED);

                            return;
                        }

                        // Match with opposite orders
                        $oppositeType = $order->type === Order::TYPE_BUY ? Order::TYPE_SELL : Order::TYPE_BUY;

                        $matches = $this->getMatchOrdersByTypeAndPrice($oppositeType, $order->price);
                        if ($matches->isEmpty()) {
                            return;
                        }

                        foreach ($matches as $matchOrder) {
                            $matchFilled = $this->orderTransactionRepository->getTransactionsAmount($matchOrder);

                            $matchRemaining = $matchOrder->amount - $matchFilled;

                            if ($matchRemaining <= 0) {
                                $this->updateOrderStatus($matchOrder, Order::STATUS_COMPLETED);
                                continue;
                            }

                            $tradable = min($remaining, $matchRemaining);

                            $fee = (new FeeService)::calculateFee($tradable, $order->price);

                            $orderTransaction = $this->orderTransactionRepository->createOrder([
                                'buy_order_id'  => $order->type === Order::TYPE_BUY ? $order->id : $matchOrder->id,
                                'sell_order_id' => $order->type === Order::TYPE_SELL ? $order->id : $matchOrder->id,
                                'amount'        => $tradable,
                                'price'         => $order->price,
                                'fee'           => $fee,
                            ]);

                            \App\Jobs\UpdateUserGoldBalance::dispatch($orderTransaction);

                            $remaining -= $tradable;

                            // Update status of matched order
                            if ($matchRemaining - $tradable <= 0) {
                                $this->updateOrderStatus($matchOrder, Order::STATUS_COMPLETED);
                            } else {
                                $this->updateOrderStatus($matchOrder, Order::STATUS_PARTIAL);
                            }

                            if ($remaining <= 0) {
                                break;
                            }
                        }

                        // Update current order status
                        if ($remaining <= 0) {
                            $this->updateOrderStatus($order, Order::STATUS_COMPLETED);
                        } elseif ($remaining < $order->amount) {
                            $this->updateOrderStatus($order, Order::STATUS_PARTIAL);
                        }
                    });
                }
            });

        return __('success');
    }

    public function getMatchOrdersByTypeAndPrice($type, $price)
    {
        return Order::where('type', $type)
            ->where('price', $price)
            ->whereIn('status', [Order::STATUS_OPEN, Order::STATUS_PARTIAL])
            ->orderBy('created_at')
            ->lockForUpdate()
            ->get();
    }

    public function updateOrderStatus($order, $status)
    {
        $order->status = $status;
        $order->save();

        return $order;
    }
}
