<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'user_id',
        'type',
        'amount',
        'price',
        'status',
        'description',
    ];

    const TYPE_BUY  = 1;
    const TYPE_SELL = 2;

    const TYPE_TEXT = [
        self::TYPE_BUY  => 'خرید',
        self::TYPE_SELL => 'فروش',
    ];

    const STATUS_CANCELED = -1;
    const STATUS_OPEN      = 0;
    const STATUS_PARTIAL   = 1;
    const STATUS_COMPLETED = 2;

    const STATUS_TEXT = [
        self::STATUS_CANCELED => 'کنسل شده',
        self::STATUS_OPEN      => 'باز',
        self::STATUS_PARTIAL   => 'جزئی',
        self::STATUS_COMPLETED => 'تکمیل شده',
    ];

    public function getOrderTransactionsAttribute()
    {
        return OrderTransaction::where('buy_order_id', $this->id)
            ->orWhere('sell_order_id', $this->id)
            ->get();
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
