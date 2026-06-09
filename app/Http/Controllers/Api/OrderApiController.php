<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;

class OrderApiController extends Controller
{
    // GET /api/orders
    public function index(Request $request)
    {
        $query = Order::with('member');

        if ($request->search) {
            $query->whereHas('member', function ($q) use ($request) {
                $q->where('first_name', 'like', '%' . $request->search . '%')
                  ->orWhere('last_name',  'like', '%' . $request->search . '%')
                  ->orWhere('seller_id',  'like', '%' . $request->search . '%');
            })->orWhere('order_no', 'like', '%' . $request->search . '%');
        }
        if ($request->date_from) {
            $query->whereDate('order_date', '>=', $request->date_from);
        }
        if ($request->date_to) {
            $query->whereDate('order_date', '<=', $request->date_to);
        }
        if ($request->city) {
            $query->where('city', 'like', '%' . $request->city . '%');
        }

        return response()->json([
            'success' => true,
            'data'    => $query->latest()->paginate(15),
        ]);
    }

    // POST /api/orders
    public function store(Request $request)
    {
        $request->validate([
            'member_id'      => 'required|exists:members,id',
            'order_date'     => 'required|date',
            'city'           => 'required|string|max:100',
            'order_product'  => 'required|string|max:255',
            'order_quantity' => 'required|integer|min:1',
            'order_value'    => 'required|numeric|min:0',
        ]);

        $order = Order::create([
            'order_no'       => Order::generateOrderNo(),
            'member_id'      => $request->member_id,
            'order_date'     => $request->order_date,
            'city'           => $request->city,
            'order_product'  => $request->order_product,
            'order_quantity' => $request->order_quantity,
            'order_value'    => $request->order_value,
            'status'         => 'pending',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Order created successfully.',
            'data'    => $order->load('member'),
        ], 201);
    }

    // GET /api/orders/{order}
    public function show(Order $order)
    {
        return response()->json([
            'success' => true,
            'data'    => $order->load('member'),
        ]);
    }

    // PUT /api/orders/{order}
    public function update(Request $request, Order $order)
    {
        $request->validate([
            'member_id'      => 'required|exists:members,id',
            'order_date'     => 'required|date',
            'city'           => 'required|string|max:100',
            'order_product'  => 'required|string|max:255',
            'order_quantity' => 'required|integer|min:1',
            'order_value'    => 'required|numeric|min:0',
            'status'         => 'required|in:pending,processing,delivered,cancelled',
        ]);

        $order->update([
            'member_id'      => $request->member_id,
            'order_date'     => $request->order_date,
            'city'           => $request->city,
            'order_product'  => $request->order_product,
            'order_quantity' => $request->order_quantity,
            'order_value'    => $request->order_value,
            'status'         => $request->status,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Order updated successfully.',
            'data'    => $order->load('member'),
        ]);
    }

    // DELETE /api/orders/{order}
    public function destroy(Order $order)
    {
        $order->delete();

        return response()->json([
            'success' => true,
            'message' => 'Order deleted successfully.',
        ]);
    }

    // GET /api/orders/{order}/items
    public function items(Order $order)
    {
        return response()->json([
            'success' => true,
            'data'    => $order->load('member'),
        ]);
    }

    // PUT /api/orders/{order}/status
    public function updateStatus(Request $request, Order $order)
    {
        $request->validate([
            'status' => 'required|in:pending,processing,delivered,cancelled',
        ]);

        $order->update(['status' => $request->status]);

        return response()->json([
            'success' => true,
            'message' => 'Order status updated.',
            'data'    => $order->fresh('member'),
        ]);
    }

    // GET /api/orders/{order}/action
    public function action(Order $order)
    {
        return response()->json([
            'success' => true,
            'data'    => $order->load('member'),
        ]);
    }

    // PUT /api/orders/{order}/action
    public function actionUpdate(Request $request, Order $order)
    {
        $request->validate([
            'status' => 'required|in:pending,processing,delivered,cancelled',
        ]);

        $order->update([
            'status' => $request->status,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Order action updated.',
            'data'    => $order->fresh('member'),
        ]);
    }
}