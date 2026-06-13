<?php

namespace App\Http\Controllers;

use App\Models\Member;
use App\Models\Order;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $totalMembers     = Member::count();
        $totalLeft        = Member::where('position', 'left')->count();
        $totalRight       = Member::where('position', 'right')->count();
        $totalOrderQty    = Order::sum('order_quantity');
        $totalOrderValue  = Order::sum('total_value');
        $totalIncome      = Member::sum('total_income');
        // $totalCommission  = Member::selectRaw('
        //     SUM(direct_commission + level1_commission + level2_commission + new_joinee_income) as total
        // ')->value('total') ?? 0;

            // compute component sums to derive percentages
            // $directIncome       = Member::sum('direct_commission');
            // $level1Income       = Member::sum('level1_commission');
            // $level2Income       = Member::sum('level2_commission');
            // $newJoineeIncome    = Member::sum('new_joinee_income');

            // // team income is level 1 + level 2
            // $teamIncome = $level1Income + $level2Income;

            // // percentages relative to total commission (guard divide-by-zero)
            // $teamIncomePercentage = $totalCommission ? round(($teamIncome / $totalCommission) * 100, 2) : 0;
            // $directSponsorIncomePercentage = $totalCommission ? round(($directIncome / $totalCommission) * 100, 2) : 0;

        return view('dashboard', compact(
            'totalMembers',
            'totalLeft',
            'totalRight',
            'totalOrderQty',
            'totalOrderValue',
            'totalIncome',
            // 'totalCommission',
            // 'directSponsorIncomePercentage'
        ));
    }
}