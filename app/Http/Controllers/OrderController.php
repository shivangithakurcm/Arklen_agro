<?php
namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Member;
use App\Models\Product;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $query = Order::with(['member', 'product']);

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

        $orders   = $query->latest()->paginate(15);
        $members  = Member::select('id','first_name','last_name','seller_id')->get();
        $products = Product::orderBy('product_name')->get();

        return view('orders.index', compact('orders', 'members', 'products'));
    }

    public function store(Request $request)
    {
        Order::create([
            'order_no'       => Order::generateOrderNo(),
            'member_id'      => $request->member_id,
            'product_id'     => $request->product_id,
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
        $members  = Member::select('id','first_name','last_name','seller_id')->get();
        $products = Product::orderBy('product_name')->get();
        return view('orders.edit', compact('order', 'members', 'products'));
    }

    public function update(Request $request, Order $order)
    {
        $order->update([
            'member_id'      => $request->member_id,
            'product_id'     => $request->product_id,
            'order_quantity' => $request->order_quantity,
            'order_date'     => $request->order_date,
            'order_value'    => $request->order_value,
            'city'           => $request->city,
            'status'         => $request->status,
        ]);

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