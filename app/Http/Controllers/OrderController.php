<?php
namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Member;
use App\Models\Product;
use App\Models\SubOrder;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $query = Order::with(['member', 'subOrders']);

        if ($request->search) {
            $query->where('order_no', 'like', '%'.$request->search.'%')
                  ->orWhereHas('member', function($q) use ($request) {
                      $q->where('first_name', 'like', '%'.$request->search.'%')
                        ->orWhere('last_name',  'like', '%'.$request->search.'%')
                        ->orWhere('seller_id',  'like', '%'.$request->search.'%');
                  });
        }

        if ($request->date_from) {
            $query->whereDate('order_date', '>=', $request->date_from);
        }
        if ($request->date_to) {
            $query->whereDate('order_date', '<=', $request->date_to);
        }

        $orders   = $query->latest()->get();
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

    public function store(Request $request)
    {
        $product = Product::first();
        $rows    = array_filter($request->rows ?? [], fn($r) => !empty($r['name']));

        if (empty($rows)) {
            return back()->withErrors(['rows' => 'Kam se kam ek row bharein.'])->withInput();
        }

        $grandTotal = collect($rows)->sum(fn($r) => floatval($r['amount'] ?? 0));
        $qty        = count($rows);

        // ✅ Punch By — logged in user se Order ka member set karo
        $punchBy = $request->punch_by
            ? strtoupper($request->punch_by)
            : null;

        $orderMember = $punchBy
            ? Member::where('seller_id', $punchBy)->first()
            : null;

        // Fallback — Admin
        if (!$orderMember) {
            $orderMember = Member::where('seller_id', 'ADMIN001')->first();
        }

        if (!$orderMember) {
            return back()->withErrors(['rows' => 'Punch By member not found.'])->withInput();
        }

        // ✅ Order banao — member_id = punch by wala
        $order = Order::create([
            'order_no'       => Order::generateOrderNo(),
            'member_id'      => $orderMember->id,
            'product_id'     => $product->id,
            'order_quantity' => $qty,
            'order_date'     => $request->order_date,
            'order_value'    => $grandTotal,
            'order_product'  => $product->product_name,
            'total_value'    => $grandTotal,
            'total_bv'       => round(($product->business_value / 100) * $grandTotal, 2),
            'status'         => 'pending',
        ]);

        // ✅ Har row ke liye SubOrder + BV update
        // Commission yahan nahi hogi — Member bante waqt hogi (MemberController@store)
        foreach ($rows as $row) {
            SubOrder::create([
                'order_id'   => $order->id,
                'name'       => $row['name'],
                'aadhar'     => $row['aadhar']   ?? null,
                'mobile'     => $row['mobile']   ?? null,
                'sponsor_id' => !empty($row['sponsor_id']) ? strtoupper($row['sponsor_id']) : null,
                'leg'        => $row['leg']       ?? 'left',
                'amount'     => floatval($row['amount'] ?? 0),
            ]);

            // ✅ Sponsor ka BV update
            if (!empty($row['sponsor_id'])) {
                $sponsor = Member::where('seller_id', strtoupper($row['sponsor_id']))->first();
                if ($sponsor) {
                    $bv = round(($product->business_value / 100) * floatval($row['amount'] ?? 0), 2);
                    if (($row['leg'] ?? 'left') === 'left') {
                        $sponsor->increment('bv_left', $bv);
                    } else {
                        $sponsor->increment('bv_right', $bv);
                    }
                }
            }
        }

        return redirect()->route('orders.index')->with('success', 'Order successfully created!');
    }

    public function show(Order $order)
    {
        $order->load(['member', 'product', 'subOrders']);
        return view('orders.order_show', compact('order'));
    }

    public function invoice(Order $order)
    {
        return view('orders.invoice', compact('order'));
    }

    public function edit(Order $order)
    {
        $members  = Member::select('id','first_name','last_name','seller_id')->get();
        $products = Product::orderBy('product_name')->get();
        return view('orders.edit', compact('order', 'members', 'products'));
    }

    public function update(Request $request, Order $order)
    {
        $oldProduct = Product::find($order->product_id);
        $oldQty     = $order->order_quantity;
        $oldMember  = Member::find($order->member_id);

        if ($oldMember && $oldProduct) {
            $oldMember->reverseCommission($oldProduct, $oldQty);
        }

        $product = Product::find($request->product_id);
        $qty     = $request->order_quantity ?? 1;

        $order->update([
            'member_id'      => $request->member_id,
            'product_id'     => $request->product_id,
            'order_quantity' => $qty,
            'order_date'     => $request->order_date,
            'order_value'    => $product->product_price * $qty,
            'order_product'  => $product->product_name,
            'total_value'    => $product->product_price * $qty,
            'total_bv'       => round(($product->business_value / 100) * $product->product_price * $qty, 2),
            'status'         => $request->status,
        ]);

        $newMember = Member::find($request->member_id);
        if ($newMember) {
            $newMember->distributeCommission($product, $qty);
        }

        return redirect()->route('orders.index')->with('success', 'Order updated successfully!');
    }

    public function action(Order $order)
    {
        $order->load('subOrders');
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