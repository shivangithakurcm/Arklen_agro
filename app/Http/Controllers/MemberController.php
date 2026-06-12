<?php

namespace App\Http\Controllers;

use App\Models\Member;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use App\Models\Order;

class MemberController extends Controller
{
    public function index(Request $request)
{
    $query = Member::query();

    if ($request->filled('sponsor_id')) {
        $query->where('sponsor_id', $request->sponsor_id);
    }
    if ($request->filled('search')) {
        $query->where(function($q) use ($request) {
            $q->where('first_name', 'like', '%'.$request->search.'%')
              ->orWhere('last_name',  'like', '%'.$request->search.'%')
              ->orWhere('contact',    'like', '%'.$request->search.'%');
        });
    }

    $members      = $query->latest()->paginate(5);
    $allMembers   = Member::where('is_active', 1)        // ← sirf active
                          ->orderBy('first_name')
                          ->get(['id', 'seller_id', 'first_name', 'last_name']); // ← sirf zaroori columns
    $products     = Product::orderBy('product_name')->get();
    $nextSellerId = Member::generateSellerId();           // ← add kiya

    return view('members.index', compact('members', 'allMembers', 'products', 'nextSellerId'));
}

 public function store(Request $request)
{
    $validator = \Validator::make($request->all(), [
        'first_name'      => 'required|string|max:100',
        'last_name'       => 'required|string|max:100',
        'contact'         => 'required|digits:10',
        'address'         => 'required|string',
        'aadhar_no'       => 'nullable|digits:12',
        'city'             => 'nullable',
        'date_of_joining' => 'required|date',
        'sponsor_id'      => 'nullable|string',
        'sponsor_leg'     => 'nullable|in:left,right',
        'password'        => 'required|min:4|confirmed',
        'profile_image'   => 'nullable|image|max:2048',
        
    ]);

    if ($validator->fails()) {
        return redirect()->route('members.index')
            ->withErrors($validator)
            ->withInput();
    }

    $leg  = $request->sponsor_leg ?? 'left';
    $data = $request->except(['password', 'password_confirmation', 'profile_image', 'sponsor_leg']);
    $data['password'] = Hash::make($request->password);
    $data['position'] = $leg;
    $data['seller_id'] = Member::generateSellerId();
   

    if ($request->filled('sponsor_id')) {
        $data['parent_id'] = $this->findAvailableParent($request->sponsor_id, $leg);
    }

    if ($request->hasFile('profile_image')) {
        $data['profile_image'] = $request->file('profile_image')->store('profiles', 'public');
    }

    $member = Member::create($data);

    \App\Models\User::updateOrCreate(
    ['seller_id' => $data['seller_id']],          // search condition
    [
        'name'     => $request->first_name . ' ' . $request->last_name,
        'password' => Hash::make($request->password),
    ]
);

    // ✅ Order automatically create karo
    $product = Product::find($member->product_id);
    if ($product) {
        \App\Models\Order::create([
            'order_no' => 'ORD-' . strtoupper(uniqid()),
            'member_id'      => $member->id,
            'product_id'     => $member->product_id,
            'order_date'     => $member->date_of_joining,
            'city'           => $member->city,
            'order_product'  => $product->product_name,
            'order_quantity' => 1,
            'order_value'    => $product->product_price,
            'total_value'    => $product->product_price,
            'total_bv'       => round(($product->business_value / 100) * $product->product_price, 2),
            'status'         => 'delivered',
        ]);
    }

   
    return redirect()->route('members.index')->with('success', 'Member added successfully!');
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

    public function show(Member $member)
    {
        return view('members.seller-profile', compact('member'));
    }

   public function edit(Member $member)
{
    $sponsors = Member::where('id', '!=', $member->id)
                      ->where('is_active', 1)
                      ->orderBy('first_name')
                      ->get(['id', 'seller_id', 'first_name', 'last_name']);

    return view('members.edit', compact('member', 'sponsors'));
}

   public function update(Request $request, Member $member)
{
    $request->validate([
        'first_name'      => 'required|string|max:100',
        'last_name'       => 'required|string|max:100',
        'contact'         => 'required|digits:10',
        'address'         => 'required|string',
        'aadhar_no'       => 'nullable|digits:12',
        'date_of_joining' => 'required|date',
        'sponsor_id'      => 'nullable|string',
        'position'        => 'nullable|in:left,right',
        'is_active'       => 'nullable|boolean',
        'profile_image'   => 'nullable|image|max:2048',
        'password'        => 'nullable|min:4|confirmed', // ← ADD
    ]);

    $data = $request->except(['profile_image', '_method', '_token', 'password', 'password_confirmation']);

    // Password sirf tab update karo jab filled ho
    if ($request->filled('password')) {
        $data['password'] = Hash::make($request->password);
    }

    if ($request->hasFile('profile_image')) {
        if ($member->profile_image) {
            Storage::disk('public')->delete($member->profile_image);
        }
        $data['profile_image'] = $request->file('profile_image')->store('profiles', 'public');
    }

    $member->update($data);
    return redirect()->route('dashboard')->with('success', 'Member updated successfully!');
}

    public function action(Member $member)
    {
        return view('members.action', compact('member'));
    }

    public function actionUpdate(Request $request, Member $member)
    {
        $request->validate([
            'bv_left'               => 'nullable|numeric|min:0',
            'bv_right'              => 'nullable|numeric|min:0',
            'sponsor_income'        => 'nullable|numeric|min:0',
            'direct_sponsor_income' => 'nullable|numeric|min:0',
            'team_income'           => 'nullable|numeric|min:0',
            'pay_income'            => 'nullable|numeric|min:0',
            'total_income'          => 'nullable|numeric|min:0',
            'team_bv'               => 'nullable|numeric|min:0',
        ]);

        $member->update($request->only([
            'bv_left', 'bv_right', 'sponsor_income', 'direct_sponsor_income',
            'team_income', 'pay_income', 'total_income', 'team_bv',
        ]));

        return redirect()->route('dashboard')->with('success', 'Updated successfully!');
    }

  
public function tree()
{
    $members = Member::select('id','seller_id','sponsor_id','parent_id','first_name','last_name','position','product_id')
                     ->get()
                     ->keyBy('seller_id');
    return view('tree', compact('members'));
}
}