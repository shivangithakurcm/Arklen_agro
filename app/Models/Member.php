<?php

namespace App\Models;

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
        'team_bv', 'is_active','parent_id', 'product_id',// fillable mein add karo
'direct_commission', 'level1_commission', 'level2_commission',
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

    public function leftMembers()
    {
        return self::where('sponsor_id', $this->seller_id)
                   ->where('position', 'left')->get();
    }

    public function rightMembers()
    {
        return self::where('sponsor_id', $this->seller_id)
                   ->where('position', 'right')->get();
    }

    public function sponsor()
    {
        return $this->sponsor_id
            ? self::where('seller_id', $this->sponsor_id)->first()
            : null;
    }
    public function product()
{
    return $this->belongsTo(Product::class);
}
// Sponsor ka Sponsor (Level 1 wala)
public function level1Sponsor()
{
    return $this->sponsor()
        ? $this->sponsor()->sponsor()
        : null;
}

// Sponsor ka Sponsor ka Sponsor (Level 2 wala)
public function level2Sponsor()
{
    return $this->level1Sponsor()
        ? $this->level1Sponsor()->sponsor()
        : null;
}

// Commission calculate karke save karo
public function distributeCommission()
{
    $product = $this->product;
    if (!$product) return;

    $price = $product->product_price ?? 0;
    $pct   = fn($field) => round(($product->{$field} ?? 0) / 100 * $price, 2);

    $directSponsor = $this->sponsor();

    // ── 1. Direct Commission ──
    if ($directSponsor) {
        $amount = $pct('direct_commission');
        if ($amount > 0) {
            $directSponsor->increment('direct_commission', $amount);
            $directSponsor->increment('total_income',      $amount);
            $directSponsor->increment('balance',           $amount);
        }
    }

    // ── 2. New Joinee Commission ──
    if ($directSponsor) {
        $amount = $pct('new_joinee');
        if ($amount > 0) {
            $directSponsor->increment('new_joinee_income', $amount);
            $directSponsor->increment('total_income',      $amount);
            $directSponsor->increment('balance',           $amount);
        }
    }

    // ── 3. Level 1 Commission ──
    $level1 = $this->level1Sponsor();
    if ($level1) {
        $amount = $pct('level_1');
        if ($amount > 0) {
            $level1->increment('level1_commission', $amount);
            $level1->increment('total_income',      $amount);
            $level1->increment('balance',           $amount);
        }
    }

    // ── 4. Level 2 Commission ──
    $level2 = $this->level2Sponsor();
    if ($level2) {
        $amount = $pct('level_2');
        if ($amount > 0) {
            $level2->increment('level2_commission', $amount);
            $level2->increment('total_income',      $amount);
            $level2->increment('balance',           $amount);
        }
    }
}
// Member.php

public static function generateSellerId(): string
{
    // Sirf SL wale IDs lo, number extract karo, max nikalo
    $max = self::where('seller_id', 'like', 'SL%')
               ->get()
               ->map(fn($m) => (int) substr($m->seller_id, 2))
               ->max();

    $num = ($max ?? 0) + 1;

    // Duplicate check
    while (self::where('seller_id', 'SL' . str_pad($num, 3, '0', STR_PAD_LEFT))->exists()) {
        $num++;
    }

    return 'SL' . str_pad($num, 3, '0', STR_PAD_LEFT);
}
}