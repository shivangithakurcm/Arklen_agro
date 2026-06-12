<?php

namespace App\Models;

use App\Models\Product;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Member extends Authenticatable
{
    use Notifiable;

    protected $table = 'members';

    protected $fillable = [
        'seller_id', 'sponsor_id', 'position',
        'first_name', 'last_name', 'contact', 'address',
        'aadhar_no', 'profile_image', 'date_of_joining', 'password',
        'bv_left', 'bv_right', 'sponsor_income', 'direct_sponsor_income',
        'team_income', 'pay_income', 'total_income', 'balance',
        'team_bv', 'is_active', 'parent_id', 'product_id',
        'direct_commission', 'level1_commission', 'level2_commission',
        'new_joinee_income', 'city',  
    ];

    protected $hidden = ['password', 'remember_token'];

    protected $casts = [
        'date_of_joining' => 'date',
        'is_active'       => 'boolean',
    ];

    public function getFullNameAttribute(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }

    public function getInitialsAttribute(): string
    {
        return strtoupper(substr($this->first_name, 0, 1) . substr($this->last_name, 0, 1));
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function reverseCommission(Product $product = null, int $qty = 1)
{
    $product = $product ?? $this->product;
    if (!$product) return;

    $price = ($product->product_price ?? 0) * $qty;
    $pct   = fn($field) => round(($product->{$field} ?? 0) / 100 * $price, 2);

    $directParent = Member::where('seller_id', $this->parent_id)->first();
    $level1Parent = $directParent
                    ? Member::where('seller_id', $directParent->parent_id)->first()
                    : null;
    $level2Parent = $level1Parent
                    ? Member::where('seller_id', $level1Parent->parent_id)->first()
                    : null;

    // ← Pehla order check (reverse mein bhi same check)
    $isFirstOrder = \App\Models\Order::where('member_id', $this->id)->count() <= 1;

    if ($directParent) {
        $amount = $pct('direct_commission');
        if ($amount > 0) {
            $directParent->decrement('direct_commission', $amount);
            $directParent->decrement('total_income',      $amount);
            $directParent->decrement('balance',           $amount);
        }

        // ← Sirf pehle order ka reverse
        if ($isFirstOrder) {
            $amount = $pct('new_joinee');
            if ($amount > 0) {
                $directParent->decrement('new_joinee_income', $amount);
                $directParent->decrement('total_income',      $amount);
                $directParent->decrement('balance',           $amount);
            }
        }
    }

    if ($level1Parent) {
        $amount = $pct('level_1');
        if ($amount > 0) {
            $level1Parent->decrement('level1_commission', $amount);
            $level1Parent->decrement('total_income',      $amount);
            $level1Parent->decrement('balance',           $amount);
        }
    }

    if ($level2Parent) {
        $amount = $pct('level_2');
        if ($amount > 0) {
            $level2Parent->decrement('level2_commission', $amount);
            $level2Parent->decrement('total_income',      $amount);
            $level2Parent->decrement('balance',           $amount);
        }
    }
}

    public function leftMembers()
    {
        return self::where('parent_id', $this->seller_id)
                   ->where('position', 'left')
                   ->get();
    }

    public function rightMembers()
    {
        return self::where('parent_id', $this->seller_id)
                   ->where('position', 'right')
                   ->get();
    }

    public function sponsor()
    {
        return $this->sponsor_id
            ? self::where('seller_id', $this->sponsor_id)->first()
            : null;
    }

   

    // ← Product parameter add kiya
   public function distributeCommission(Product $product = null, int $qty = 1)
{
    $product = $product ?? $this->product;
    if (!$product) return;

    $price = ($product->product_price ?? 0) * $qty;
    $pct   = fn($field) => round(($product->{$field} ?? 0) / 100 * $price, 2);

    $directParent = Member::where('seller_id', $this->parent_id)->first();
    $level1Parent = $directParent
                    ? Member::where('seller_id', $directParent->parent_id)->first()
                    : null;
    $level2Parent = $level1Parent
                    ? Member::where('seller_id', $level1Parent->parent_id)->first()
                    : null;

    // ← Pehla order check
    $isFirstOrder = \App\Models\Order::where('member_id', $this->id)->count() === 1;

    if ($directParent) {
        $amount = $pct('direct_commission');
        if ($amount > 0) {
            $directParent->increment('direct_commission', $amount);
            $directParent->increment('total_income',      $amount);
            $directParent->increment('balance',           $amount);
        }

        // ← Sirf pehle order pe
        if ($isFirstOrder) {
            $amount = $pct('new_joinee');
            if ($amount > 0) {
                $directParent->increment('new_joinee_income', $amount);
                $directParent->increment('total_income',      $amount);
                $directParent->increment('balance',           $amount);
            }
        }
    }

    if ($level1Parent) {
        $amount = $pct('level_1');
        if ($amount > 0) {
            $level1Parent->increment('level1_commission', $amount);
            $level1Parent->increment('total_income',      $amount);
            $level1Parent->increment('balance',           $amount);
        }
    }

    if ($level2Parent) {
        $amount = $pct('level_2');
        if ($amount > 0) {
            $level2Parent->increment('level2_commission', $amount);
            $level2Parent->increment('total_income',      $amount);
            $level2Parent->increment('balance',           $amount);
        }
    }
}

public static function generateSellerId(): string
{
    do {
        $last = self::where('seller_id', 'like', 'MLM%')
                    ->orderByRaw('CAST(SUBSTRING(seller_id, 4) AS UNSIGNED) DESC')
                    ->value('seller_id');

        $nextNumber = $last
            ? (int) substr($last, 3) + 1
            : 1001;

        $newId = 'MLM' . $nextNumber;

    } while (self::where('seller_id', $newId)->exists());

    return $newId;
}
}