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
        $totalCommission  = Member::selectRaw('
            SUM(direct_commission + level1_commission + level2_commission + new_joinee_income) as total
        ')->value('total') ?? 0;

        return view('dashboard', compact(
            'totalMembers',
            'totalLeft',
            'totalRight',
            'totalOrderQty',
            'totalOrderValue',
            'totalIncome',
            'totalCommission'
        ));
    }
}