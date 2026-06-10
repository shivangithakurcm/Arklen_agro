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
        'team_bv', 'is_active', 'parent_id', 'product_id',
        'direct_commission', 'level1_commission', 'level2_commission',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected $casts = [
        'date_of_joining' => 'date',
        'is_active'       => 'boolean',
    ];

    // ── Accessors ────────────────────────────────────────────
    public function getFullNameAttribute(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }

    public function getInitialsAttribute(): string
    {
        return strtoupper(substr($this->first_name, 0, 1) . substr($this->last_name, 0, 1));
    }

    // ── Relationships ─────────────────────────────────────────
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function leftMembers()
    {
        return self::where('sponsor_id', $this->seller_id)
                   ->where('position', 'left')
                   ->get();
    }

    public function rightMembers()
    {
        return self::where('sponsor_id', $this->seller_id)
                   ->where('position', 'right')
                   ->get();
    }

    public function sponsor()
    {
        return $this->sponsor_id
            ? self::where('seller_id', $this->sponsor_id)->first()
            : null;
    }

    // ── Sponsor Chain ─────────────────────────────────────────
    public function level1Sponsor()
    {
        return $this->sponsor()
            ? $this->sponsor()->sponsor()
            : null;
    }

    public function level2Sponsor()
    {
        return $this->level1Sponsor()
            ? $this->level1Sponsor()->sponsor()
            : null;
    }

    // ── Seller ID Generator ───────────────────────────────────
    public static function generateSellerId(): string
    {
        $last       = self::orderBy('id', 'desc')->first();
        $nextNumber = $last ? ($last->id + 1) : 1;
        return 'SEL-' . str_pad($nextNumber, 5, '0', STR_PAD_LEFT);
        // Output: SEL-00001, SEL-00002, SEL-00003 ...
    }

    // ── Commission Distribution ───────────────────────────────
    public function distributeCommission()
    {
        $product = $this->product;
        if (!$product) return;

        $price = $product->product_price ?? 0;
        $pct   = fn($field) => round(($product->{$field} ?? 0) / 100 * $price, 2);

        // ── Parent Chain (tree ke upar) ──
        $directParent = Member::where('seller_id', $this->parent_id)->first();
        $level1Parent = $directParent
                        ? Member::where('seller_id', $directParent->parent_id)->first()
                        : null;
        $level2Parent = $level1Parent
                        ? Member::where('seller_id', $level1Parent->parent_id)->first()
                        : null;

        // ── 1. Direct Commission (immediate parent ko) ──
        if ($directParent) {
            $amount = $pct('direct_commission');
            if ($amount > 0) {
                $directParent->increment('direct_commission', $amount);
                $directParent->increment('total_income',      $amount);
                $directParent->increment('balance',           $amount);
            }
        }

        // ── 2. New Joinee Commission (immediate parent ko) ──
        if ($directParent) {
            $amount = $pct('new_joinee');
            if ($amount > 0) {
                $directParent->increment('new_joinee_income', $amount);
                $directParent->increment('total_income',      $amount);
                $directParent->increment('balance',           $amount);
            }
        }

        // ── 3. Level 1 Commission ──
        if ($level1Parent) {
            $amount = $pct('level_1');
            if ($amount > 0) {
                $level1Parent->increment('level1_commission', $amount);
                $level1Parent->increment('total_income',      $amount);
                $level1Parent->increment('balance',           $amount);
            }
        }

        // ── 4. Level 2 Commission ──
        if ($level2Parent) {
            $amount = $pct('level_2');
            if ($amount > 0) {
                $level2Parent->increment('level2_commission', $amount);
                $level2Parent->increment('total_income',      $amount);
                $level2Parent->increment('balance',           $amount);
            }
        }
    }
}