<?php
namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Member;
use App\Models\Product;
use App\Models\SubOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\City;

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

        $orders   = $query->latest()->paginate(3);
        $members  = Member::select('id','first_name','last_name','seller_id')->get();
        $products = Product::orderBy('product_name')->get();

        return view('orders.index', compact('orders', 'members', 'products'));
    }

   public function create()
{
    $members  = Member::orderBy('first_name')->get();
    $products = Product::all();
    $cities   = City::orderBy('name')->get();

    return view('orders.create', compact(
        'members',
        'products',
        'cities'
    ));
}

    public function store(Request $request)
    {
        $rows = array_filter($request->rows ?? [], fn($r) => !empty($r['name']));

        if (empty($rows)) {
            return back()->withErrors(['rows' => 'Fill at least one row.'])->withInput();
        }

        $grandTotal = collect($rows)->sum(fn($r) => floatval($r['amount'] ?? 0));
        $qty        = count($rows);

        // ✅ Punch By
        $punchBy     = $request->punch_by ? strtoupper($request->punch_by) : null;
        $orderMember = $punchBy ? Member::where('seller_id', $punchBy)->first() : null;

        if (!$orderMember) {
            $orderMember = Member::where('seller_id', 'ADMIN001')->first();
        }

        if (!$orderMember) {
            return back()->withErrors(['rows' => 'Punch By member not found.'])->withInput();
        }

        // ✅ Pehli row ka product Order-level ke liye
        $firstProductId = collect($rows)->first()['product_id'] ?? null;
        $firstProduct   = ($firstProductId ? Product::find($firstProductId) : null)
                          ?? Product::first();

        // ✅ Total BV — har row ka apna product se
        $totalBv = collect($rows)->sum(function ($r) {
            $p = !empty($r['product_id']) ? Product::find($r['product_id']) : null;
            return $p
                ? round(($p->business_value / 100) * floatval($r['amount'] ?? 0), 2)
                : 0;
        });

        // ✅ Order create
        $order = Order::create([
            'order_no'       => Order::generateOrderNo(),
            'member_id'      => $orderMember->id,
            'product_id'     => $firstProduct->id,
            'order_quantity' => $qty,
            'order_date'     => $request->order_date,
            'order_value'    => $grandTotal,
            'order_product'  => $firstProduct->product_name,
            'total_value'    => $grandTotal,
            'total_bv'       => $totalBv,
            'status'         => 'pending',
        ]);

        // ✅ Har row ke liye SubOrder + BV
        foreach ($rows as $row) {
            $product = (!empty($row['product_id']) ? Product::find($row['product_id']) : null)
                       ?? $firstProduct;

            $member = null;
            if (!empty($row['modal_first_name']) && !empty($row['modal_password'])) {
                $memberData = [
                    'first_name'   => $row['modal_first_name'],
                    'last_name'    => $row['modal_last_name'] ?? '',
                    'contact'      => $row['modal_mobile'] ?? $row['mobile'] ?? null,
                    'aadhar_no'    => $row['modal_aadhar'] ?? $row['aadhar'] ?? null,
                    'sponsor_id'   => !empty($row['modal_sponsor']) ? strtoupper($row['modal_sponsor']) : null,
                    'position'     => $row['modal_leg'] ?? 'left',
                    'city'         => $row['modal_city_select'] ?? null,
                    'address'      => $row['modal_address'] ?? null,
                    'date_of_joining' => $row['modal_date'] ?: $request->order_date,
                    'password'     => Hash::make($row['modal_password']),
                    'seller_id'    => Member::generateSellerId(),
                    'product_id'   => $product->id ?? null,
                    'parent_id'    => $this->findAvailableParent($row['modal_sponsor'] ?? '', $row['modal_leg'] ?? 'left'),
                    'is_active'    => 1,
                ];

                $existing = null;
                if (!empty($memberData['aadhar_no'])) {
                    $existing = Member::where('aadhar_no', $memberData['aadhar_no'])->first();
                }
                if (!$existing && !empty($memberData['contact'])) {
                    $existing = Member::where('contact', $memberData['contact'])->first();
                }

                if ($existing) {
                    $member = $existing;
                } else {
                    $member = Member::create($memberData);
                    if ($product) {
                        $member->distributeCommission($product, 1, floatval($row['amount'] ?? 0));
                    }
                }
            }

            SubOrder::create([
                'order_id'   => $order->id,
                'name'       => $row['name'],
                'aadhar'     => $row['aadhar']   ?? null,
                'mobile'     => $row['mobile']   ?? null,
                'sponsor_id' => !empty($row['sponsor_id']) ? strtoupper($row['sponsor_id']) : null,
                'leg'        => $row['leg']       ?? 'left',
                'amount'     => floatval($row['amount'] ?? 0),
                'product_id' => $product->id,
            ]);

            // ✅ Sponsor ka BV — us row ke product se
            if (!empty($row['sponsor_id']) && $product) {
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

        return redirect()->route('orders.action', $order)->with('success', 'Order successfully created!');
    }

    private function findAvailableParent(string $sponsorId, string $leg): ?string
    {
        $queue = [$sponsorId];

        while (!empty($queue)) {
            $currentId = array_shift($queue);
            $current   = Member::where('seller_id', $currentId)->first();

            if (!$current) break;

            $childInLeg = Member::where('parent_id', $currentId)
                                ->where('position', $leg)
                                ->first();

            if (!$childInLeg) {
                return $currentId;
            }

            $queue[] = $childInLeg->seller_id;
        }

        return null;
    }

    public function show(Order $order)
    {
        $order->load(['member', 'product', 'subOrders']);

        $subAadhars        = $order->subOrders->pluck('aadhar')->filter()->toArray();
        $registeredAadhars = Member::whereIn('aadhar_no', $subAadhars)
                                   ->pluck('aadhar_no')
                                   ->toArray();

        return view('orders.order_show', compact('order', 'registeredAadhars'));
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