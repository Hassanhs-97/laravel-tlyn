<?php

namespace App\Http\Resources\Api\V1\Order;

use App\Http\Resources\Api\V1\OrderTransaction\OrderTransactionResourceCollection;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'           => $this->id,
            'type'         => Order::TYPE_TEXT[$this->type] ?? __('undefined'),
            'amount'       => $this->amount,
            'price'        => rial_to_toman($this->price),
            'status'       => Order::STATUS_TEXT[$this->status] ?? __('undefined'),
            'transactions' => new OrderTransactionResourceCollection($this->orderTransactions),
            'created_at'   => verta($this->created_at, 'Asia/Tehran')->format('Y/m/d H:i'),

        ];
    }
}
