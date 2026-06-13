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

    public function getBusinessValue(?Product $product = null, int $qty = 1, float $amount = 0): float
    {
        $product = $product ?? $this->product;
        if (!$product) {
            return 0;
        }

        $base = $amount > 0
            ? $amount
            : (($product->product_price ?? 0) * $qty);

        return round(($product->business_value / 100) * $base, 2);
    }

    public function distributeBV(?Product $product = null, int $qty = 1, float $amount = 0): void
    {
        $bv = $this->getBusinessValue($product, $qty, $amount);
        if ($bv <= 0) {
            return;
        }

        // Walk up the placement (parent_id) chain and credit BV to each ancestor
        // based on which leg the lower node occupies relative to that ancestor.
        $child = $this; // start from the buyer
        $currentParentSellerId = $child->parent_id;

        while (!empty($currentParentSellerId)) {
            $parent = self::where('seller_id', $currentParentSellerId)->first();
            if (!$parent) break;

            $leg = $child->position ?? 'left';
            if ($leg === 'left') {
                $parent->increment('bv_left', $bv);
            } else {
                $parent->increment('bv_right', $bv);
            }

            $parent->increment('team_bv', $bv);

            // move one step up
            $child = $parent;
            $currentParentSellerId = $child->parent_id;
        }
    }

    // ✅ amount parameter add kiya — actual purchase amount se commission
    public function distributeCommission(?Product $product = null, int $qty = 1, float $amount = 0)
    {
        $product = $product ?? $this->product;
        if (!$product) return;

        $this->distributeBV($product, $qty, $amount);

        // ✅ Agar actual amount pass hua toh wahi use karo, warna product price * qty
        $price = $amount > 0 ? $amount : (($product->product_price ?? 0) * $qty);

        $pct = fn($field) => round(($product->{$field} ?? 0) / 100 * $price, 2);

        $directParent = Member::where('seller_id', $this->sponsor_id)->first();
        $level1Parent = $directParent
                        ? Member::where('seller_id', $directParent->sponsor_id)->first()
                        : null;
        $level2Parent = $level1Parent
                        ? Member::where('seller_id', $level1Parent->sponsor_id)->first()
                        : null;

        // $isFirstOrder = true;

        $newJoineeIncome = $pct('new_joinee');
        $this->increment('new_joinee_income', $newJoineeIncome);
        if ($directParent) {
            // ✅ Direct Commission
            $amount_dc = $pct('direct_commission');
            if ($amount_dc > 0) {
                $directParent->increment('direct_commission', $amount_dc);
                $directParent->increment('total_income',      $amount_dc);
                $directParent->increment('balance',           $amount_dc);
            }

            // // ✅ New Joinee — sirf pehle order par
            // if ($isFirstOrder) {
            //     $amount_nj = $pct('new_joinee');
            //     if ($amount_nj > 0) {
            //         // $directParent->increment('new_joinee_income', $amount_nj);
            //         $directParent->increment('total_income',      $amount_nj);
            //         $directParent->increment('balance',           $amount_nj);
            //     }
            // }
        }

        // ✅ Level 1
        if ($level1Parent) {
            $amount_l1 = $pct('level_1');
            if ($amount_l1 > 0) {
                $level1Parent->increment('level1_commission', $amount_l1);
                $level1Parent->increment('total_income',      $amount_l1);
                $level1Parent->increment('balance',           $amount_l1);
            }
        }

        // ✅ Level 2
        if ($level2Parent) {
            $amount_l2 = $pct('level_2');
            if ($amount_l2 > 0) {
                $level2Parent->increment('level2_commission', $amount_l2);
                $level2Parent->increment('total_income',      $amount_l2);
                $level2Parent->increment('balance',           $amount_l2);
            }
        }
    }

    // ✅ amount parameter add kiya — reverse mein bhi same logic
    public function reverseCommission(?Product $product = null, int $qty = 1, float $amount = 0)
    {
        $product = $product ?? $this->product;
        if (!$product) return;

        $price = $amount > 0 ? $amount : (($product->product_price ?? 0) * $qty);

        $pct = fn($field) => round(($product->{$field} ?? 0) / 100 * $price, 2);

        $directParent = Member::where('seller_id', $this->sponsor_id)->first();
        $level1Parent = $directParent
                        ? Member::where('seller_id', $directParent->sponsor_id)->first()
                        : null;
        $level2Parent = $level1Parent
                        ? Member::where('seller_id', $level1Parent->sponsor_id)->first()
                        : null;

        $isFirstOrder = \App\Models\Order::where('member_id', $this->id)->count() <= 1;

        if ($directParent) {
            $amount_dc = $pct('direct_commission');
            if ($amount_dc > 0) {
                $directParent->decrement('direct_commission', $amount_dc);
                $directParent->decrement('total_income',      $amount_dc);
                $directParent->decrement('balance',           $amount_dc);
            }

            if ($isFirstOrder) {
                $amount_nj = $pct('new_joinee');
                if ($amount_nj > 0) {
                    $directParent->decrement('new_joinee_income', $amount_nj);
                    $directParent->decrement('total_income',      $amount_nj);
                    $directParent->decrement('balance',           $amount_nj);
                }
            }
        }

        if ($level1Parent) {
            $amount_l1 = $pct('level_1');
            if ($amount_l1 > 0) {
                $level1Parent->decrement('level1_commission', $amount_l1);
                $level1Parent->decrement('total_income',      $amount_l1);
                $level1Parent->decrement('balance',           $amount_l1);
            }
        }

        if ($level2Parent) {
            $amount_l2 = $pct('level_2');
            if ($amount_l2 > 0) {
                $level2Parent->decrement('level2_commission', $amount_l2);
                $level2Parent->decrement('total_income',      $amount_l2);
                $level2Parent->decrement('balance',           $amount_l2);
            }
        }
    }

    // public static function generateSellerId(): string
    // {
    //     do {
    //         $last = self::where('seller_id', 'like', 'SL%')
    //                     ->orderByRaw('CAST(SUBSTRING(seller_id, 4) AS UNSIGNED) DESC')
    //                     ->value('seller_id');

    //         $nextNumber = $last
    //             ? (int) substr($last, 3) + 1
    //             : 1001;

    //         $newId = 'SL' . $nextNumber;

    //     } while (self::where('seller_id', $newId)->exists());

    //     return $newId;
    // }
    public static function generateSellerId(): string
    {
        $lastMember = self::select('id', 'seller_id')
            ->latest('id')
            ->first();

        if (!$lastMember || !$lastMember->seller_id) {
            return 'SL1001';
        }

        $number = (int) preg_replace('/\D/', '', $lastMember->seller_id);

        return 'SL' . ($number + 1);
    }
}