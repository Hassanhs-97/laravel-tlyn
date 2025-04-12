<?php

namespace App\Repositories;

use App\Models\Order;
use Illuminate\Support\Facades\Auth;

class OrderRepositoey
{

    public function getAllOrders()
    {
        return Order::where('user_id', Auth::id())
        ->latest()
        ->paginate(20);
    }

    public function getOrderById($id)
    {
        $order = Order::where('user_id', Auth::id())->lockforUpdate()->find($id);

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

    public function deleteOrder($id)
    {
        // Logic to delete an order
    }

    public function matchOrders()
    {
        return 111;
    }
}
