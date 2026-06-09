<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
   protected $fillable = [
    'product_name', 
    'product_price', 
    'product_image',
    'business_value',
    'direct_commission',
    'new_joinee',
    'level_1',
    'level_2',
];
}