<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Order extends Model
{
    protected $fillable = [
        'order_no',
        'member_id',
        'product_id',
        'order_quantity',
        'order_date',
        'order_value',
        'city',
        'status',
    ];

    protected $casts = [
        'order_date' => 'date',
    ];

    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

   public static function generateOrderNo(): string
{
    $max = self::max('id') ?? 0;
    $num = $max + 1;

    $orderNo = 'ORD' . str_pad($num, 4, '0', STR_PAD_LEFT);

    while (self::where('order_no', $orderNo)->exists()) {
        $num++;
        $orderNo = 'ORD' . str_pad($num, 4, '0', STR_PAD_LEFT);
    }

    return $orderNo;
}
}