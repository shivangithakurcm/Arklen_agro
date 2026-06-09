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
    $bv    = $product->business_value ?? 0;

    // ── 1. BV → Sponsor ke left/right mein add karo ──
    $directSponsor = $this->sponsor();
    if ($directSponsor && $bv > 0) {
        $leg = $this->position; // 'left' or 'right'

        if ($leg === 'left') {
            $directSponsor->increment('bv_left', $bv);
        } else {
            $directSponsor->increment('bv_right', $bv);
        }

        $directSponsor->increment('team_bv', $bv);
        $directSponsor->refresh();

        // ── 2. Pair Matching ──
        $matchedPairs = min($directSponsor->bv_left, $directSponsor->bv_right);
        if ($matchedPairs > 0) {
            $pairIncome = $matchedPairs * 10; // ← 1 pair = ₹10 (apni value rakho)

            $directSponsor->increment('sponsor_income', $pairIncome);
            $directSponsor->increment('total_income',   $pairIncome);
            $directSponsor->increment('balance',        $pairIncome);

            // Used BV minus karo
            $directSponsor->decrement('bv_left',  $matchedPairs);
            $directSponsor->decrement('bv_right', $matchedPairs);
        }
    }

    // ── 3. New Joinee Income → Sponsor ko milegi ──
    if ($directSponsor) {
        $newJoineeAmount = $product->new_joinee ?? 0;
        if ($newJoineeAmount > 0) {
            $directSponsor->increment('new_joinee_income', $newJoineeAmount);
            $directSponsor->increment('total_income',      $newJoineeAmount);
            $directSponsor->increment('balance',           $newJoineeAmount);
        }
    }

    // ── 4. Direct Commission → Sponsor ko ──
    if ($directSponsor) {
        $amount = $product->direct_commission ?? 0;
        if ($amount > 0) {
            $directSponsor->increment('direct_commission',     $amount);
            $directSponsor->increment('direct_sponsor_income', $amount);
            $directSponsor->increment('total_income',          $amount);
            $directSponsor->increment('balance',               $amount);
        }
    }

    // ── 5. Level 1 Commission ──
    $level1 = $this->level1Sponsor();
    if ($level1) {
        $amount = $product->level_1 ?? 0;
        if ($amount > 0) {
            $level1->increment('level1_commission', $amount);
            $level1->increment('team_income',       $amount);
            $level1->increment('total_income',      $amount);
            $level1->increment('balance',           $amount);
        }
    }

    // ── 6. Level 2 Commission ──
    $level2 = $this->level2Sponsor();
    if ($level2) {
        $amount = $product->level_2 ?? 0;
        if ($amount > 0) {
            $level2->increment('level2_commission', $amount);
            $level2->increment('team_income',       $amount);
            $level2->increment('total_income',      $amount);
            $level2->increment('balance',           $amount);
        }
    }
}
}