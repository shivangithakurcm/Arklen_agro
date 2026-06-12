<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubOrder extends Model
{
    protected $fillable = [
        'order_id',
        'name',
        'aadhar',
        'mobile',
        'sponsor_id',
        'leg',
        'amount',
        'product_id',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}