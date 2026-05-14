<?php
namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Member;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $query = Order::with('member');

        if ($request->search) {
            $query->whereHas('member', function($q) use ($request) {
                $q->where('first_name', 'like', '%'.$request->search.'%')
                  ->orWhere('last_name', 'like', '%'.$request->search.'%')
                  ->orWhere('seller_id', 'like', '%'.$request->search.'%');
            })->orWhere('order_no', 'like', '%'.$request->search.'%');
        }

        if ($request->date_from) {
            $query->whereDate('order_date', '>=', $request->date_from);
        }
        if ($request->date_to) {
            $query->whereDate('order_date', '<=', $request->date_to);
        }
        if ($request->city) {
            $query->where('city', 'like', '%'.$request->city.'%');
        }

        $orders = $query->latest()->paginate(15);
        $members = Member::select('id','first_name','last_name','seller_id')->get();

        return view('orders.index', compact('orders', 'members'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'member_id'      => 'required|exists:members,id',
            'order_product'  => 'required|string|max:255',
            'order_quantity' => 'required|integer|min:1',
            'order_date'     => 'required|date',
            'order_value'    => 'required|numeric|min:0',
            'city'           => 'required|string|max:100',
        ]);

        Order::create([
            'order_no'       => Order::generateOrderNo(),
            'member_id'      => $request->member_id,
            'order_product'  => $request->order_product,
            'order_quantity' => $request->order_quantity,
            'order_date'     => $request->order_date,
            'order_value'    => $request->order_value,
            'city'           => $request->city,
            'status'         => 'pending',
        ]);

        return redirect()->route('orders.index')->with('success', 'Order created successfully!');
    }

    public function edit(Order $order)
    {
        $members = Member::select('id','first_name','last_name','seller_id')->get();
        return view('orders.edit', compact('order', 'members'));
    }

    public function update(Request $request, Order $order)
    {
        $request->validate([
            'member_id'      => 'required|exists:members,id',
            'order_product'  => 'required|string|max:255',
            'order_quantity' => 'required|integer|min:1',
            'order_date'     => 'required|date',
            'order_value'    => 'required|numeric|min:0',
            'city'           => 'required|string|max:100',
            'status'         => 'required|in:pending,processing,delivered,cancelled',
        ]);

        $order->update($request->all());

        return redirect()->route('orders.index')->with('success', 'Order updated successfully!');
    }

    public function action(Order $order)
    {
        return view('orders.action', compact('order'));
    }

    public function actionUpdate(Request $request, Order $order)
    {
        $request->validate([
            'status' => 'required|in:pending,processing,delivered,cancelled',
        ]);

        $order->update(['status' => $request->status]);

        return redirect()->route('orders.index')->with('success', 'Order status updated!');
    }
}