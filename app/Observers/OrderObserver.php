<?php

namespace App\Observers;

use App\Models\Order;
use App\Services\CommissionService;

class OrderObserver
{
    protected CommissionService $commission;

    public function __construct(CommissionService $commission)
    {
        $this->commission = $commission;
    }

    public function created(Order $order): void
    {
        $member = \App\Models\Member::where('id', $order->member_id)->first();

        if (!$member || !$member->is_active) return;

        // BV nahi pata toh total_value use karo abhi ke liye
        $bv = $order->total_bv > 0 ? $order->total_bv : $order->total_value;

        if ($bv <= 0) return;

        $this->commission->processOrderCommission($member, (float) $bv);
    }
}