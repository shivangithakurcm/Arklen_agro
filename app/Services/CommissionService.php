<?php

namespace App\Services;

use App\Models\Member;
use Illuminate\Support\Facades\DB;

class CommissionService
{
    // Commission rates — yahan se change karo
    const DIRECT_COMMISSION_RATE = 0.10;  // 10% — B ko (jisne C ko join karaya)
    const LEVEL1_COMMISSION_RATE = 0.05;  // 5%  — A ko (B ka sponsor)
    const LEVEL2_COMMISSION_RATE = 0.02;  // 2%  — A ke upar wale ko
    const PAY_INCOME_RATE        = 0.02;  // 2%  — matching/pay bonus

    // C order karta hai
    //   → B = C ka direct sponsor → Direct Commission 10%
    //   → A = B ka sponsor        → Level 1 Commission 5%
    //   → A ke upar               → Level 2 Commission 2%
    public function processOrderCommission(Member $buyer, float $orderBV): void
    {
        DB::transaction(function () use ($buyer, $orderBV) {
            $this->updateBuyerBV($buyer, $orderBV);
            $directSponsor = $this->giveDirectCommission($buyer, $orderBV);
            $level1Sponsor = $this->giveLevel1Commission($directSponsor, $orderBV);
            $this->giveLevel2Commission($level1Sponsor, $orderBV);
            $this->givePayIncome($buyer, $orderBV);
            $this->recalcMemberTotals($buyer);
        });
    }

    // C ka BV update
    private function updateBuyerBV(Member $buyer, float $bv): void
    {
        if ($buyer->position === 'left') {
            $buyer->increment('bv_left', $bv);
        } else {
            $buyer->increment('bv_right', $bv);
        }
        $buyer->increment('team_bv', $bv);
        $buyer->refresh();
    }

    // B ko 10% Direct Commission
    private function giveDirectCommission(Member $buyer, float $orderBV): ?Member
    {
        if (!$buyer->sponsor_id) return null;

        $B = Member::where('seller_id', $buyer->sponsor_id)->first();
        if (!$B || !$B->is_active) return null;

        $income = round($orderBV * self::DIRECT_COMMISSION_RATE, 2);
        $B->increment('direct_sponsor_income', $income);
        $B->increment('sponsor_income', $income);
        $B->increment('team_bv', $orderBV);

        if ($buyer->position === 'left') {
            $B->increment('bv_left', $orderBV);
        } else {
            $B->increment('bv_right', $orderBV);
        }

        $this->recalcMemberTotals($B);
        return $B;
    }

    // A ko 5% Level 1 Commission
    private function giveLevel1Commission(?Member $B, float $orderBV): ?Member
    {
        if (!$B || !$B->sponsor_id) return null;

        $A = Member::where('seller_id', $B->sponsor_id)->first();
        if (!$A || !$A->is_active) return null;

        $income = round($orderBV * self::LEVEL1_COMMISSION_RATE, 2);
        $A->increment('team_income', $income);
        $A->increment('team_bv', $orderBV);

        $this->recalcMemberTotals($A);
        return $A;
    }

    // A ke upar wale ko 2% Level 2 Commission
    private function giveLevel2Commission(?Member $A, float $orderBV): void
    {
        if (!$A || !$A->sponsor_id) return;

        $aboveA = Member::where('seller_id', $A->sponsor_id)->first();
        if (!$aboveA || !$aboveA->is_active) return;

        $income = round($orderBV * self::LEVEL2_COMMISSION_RATE, 2);
        $aboveA->increment('team_income', $income);
        $aboveA->increment('team_bv', $orderBV);

        $this->recalcMemberTotals($aboveA);
    }

    // Pay Income — matching BV bonus
    private function givePayIncome(Member $buyer, float $orderBV): void
    {
        if (!$buyer->sponsor_id) return;

        $sponsor = Member::where('seller_id', $buyer->sponsor_id)->first();
        if (!$sponsor || !$sponsor->is_active) return;

        $matchBV = min($sponsor->bv_left, $sponsor->bv_right);
        if ($matchBV <= 0) return;

        $payIncome = round($matchBV * self::PAY_INCOME_RATE, 2);
        $sponsor->update(['pay_income' => $payIncome]);
        $this->recalcMemberTotals($sponsor);
    }

    // Total Income & Balance recalculate
    public function recalcMemberTotals(Member $member): void
    {
        $member->refresh();
        $total = $member->sponsor_income
               + $member->direct_sponsor_income
               + $member->team_income
               + $member->pay_income;

        $member->update([
            'total_income' => round($total, 2),
            'balance'      => round($total, 2),
        ]);
    }

    // Members Left / Right count
    public function getMembersCount(Member $member): array
    {
        return [
            'left'  => Member::where('sponsor_id', $member->seller_id)->where('position', 'left')->count(),
            'right' => Member::where('sponsor_id', $member->seller_id)->where('position', 'right')->count(),
        ];
    }

    // Admin override
    public function adminOverride(Member $member, array $data): void
    {
        $allowed = ['sponsor_income','direct_sponsor_income','team_income',
                    'pay_income','balance','team_bv','bv_left','bv_right'];

        $filtered = array_intersect_key($data, array_flip($allowed));

        DB::transaction(function () use ($member, $filtered) {
            $member->update($filtered);
            $this->recalcMemberTotals($member);
        });
    }
}