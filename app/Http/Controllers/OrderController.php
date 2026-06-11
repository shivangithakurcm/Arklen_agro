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

    public function create()
{
    $members  = Member::orderBy('first_name')->get();
    $products = Product::all();
    return view('orders.create', compact('members', 'products'));
}
public function invoice(Order $order)
{
    return view('orders.invoice', compact('order'));
}
public function show(Order $order)
{
    $order->load(['member', 'product', 'subOrders']);

    return view('orders.order_show', compact('order'));
}
 public function store(Request $request)
{
    $product = Product::first(); // ← sirf ek product hai, directly lo
    $qty     = $request->order_quantity ?? 1;

    $order = Order::create([
        'order_no'       => 'ORD-' . strtoupper(uniqid()),
        'member_id'      => $request->member_id,
        'product_id'     => $product->id,  // ← hardcode
        'order_quantity' => $qty,
        'order_date'     => $request->order_date,
        'order_value'    => $product->product_price,
        'order_product'  => $product->product_name,
        'total_value'    => $product->product_price * $qty,
        'total_bv'       => round(($product->business_value / 100) * $product->product_price * $qty, 2),
        'city'           => $request->city,
        'status'         => 'pending',
    ]);

    $member = Member::find($order->member_id);
    if ($member) {
        $member->distributeCommission($product, $qty);
    }

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
    // 1. Pehle purana product aur qty lo
    $oldProduct = Product::find($order->product_id);
    $oldQty     = $order->order_quantity;
    $oldMember  = Member::find($order->member_id);

    // 2. Purana commission reverse karo
    if ($oldMember && $oldProduct) {
        $oldMember->reverseCommission($oldProduct, $oldQty);
    }

    // 3. Order update karo
    $product = Product::find($request->product_id);
    $qty     = $request->order_quantity ?? 1;

    $order->update([
        'member_id'      => $request->member_id,
        'product_id'     => $request->product_id,
        'order_quantity' => $qty,
        'order_date'     => $request->order_date,
        'order_value'    => $product->product_price,
        'order_product'  => $product->product_name,
        'total_value'    => $product->product_price * $qty,
        'total_bv'       => round(($product->business_value / 100) * $product->product_price * $qty, 2),
        'city'           => $request->city,
        'status'         => $request->status,
    ]);

    // 4. Naya commission distribute karo
    $newMember = Member::find($request->member_id);
    if ($newMember) {
        $newMember->distributeCommission($product, $qty);
    }

    return redirect()->route('orders.index')->with('success', 'Order updated successfully!');
}
  public function action(Order $order)
{
    $member = $order->member;

    return view('orders.action', compact('order', 'member'));
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