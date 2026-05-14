<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'order_no', 'member_id', 'order_product',
        'order_quantity', 'order_date', 'order_value', 'city', 'status'
    ];

    protected $casts = ['order_date' => 'date'];

    public function member()
    {
        return $this->belongsTo(Member::class);
    }

    public static function generateOrderNo()
    {
        $last = self::latest('id')->first();
        $num = $last ? (intval(substr($last->order_no, 3)) + 1) : 1;
        return 'ORD' . str_pad($num, 4, '0', STR_PAD_LEFT);
    }
}