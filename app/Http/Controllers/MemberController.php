<?php

namespace App\Http\Controllers;

use App\Models\Member;
use App\Models\Product;
use App\Models\City;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use App\Models\Order;
use Log;

class MemberController extends Controller
{
    // public function index(Request $request)
    // {
    //     $query = Member::query();

    //     if ($request->filled('sponsor_id')) {
    //         $query->where('sponsor_id', $request->sponsor_id);
    //     }
    //     if ($request->filled('search')) {
    //         $query->where(function($q) use ($request) {
    //             $q->where('first_name', 'like', '%'.$request->search.'%')
    //               ->orWhere('last_name',  'like', '%'.$request->search.'%')
    //               ->orWhere('contact',    'like', '%'.$request->search.'%');
    //         });
    //     }

    //     $members      = $query->latest()->paginate(5);
    //     $allMembers   = Member::where('is_active', 1)
    //                           ->orderBy('first_name')
    //                           ->get(['id', 'seller_id', 'first_name', 'last_name']);
    //     $products     = Product::orderBy('product_name')->get();
    //     $cities       = City::orderBy('name')->get();
    //     $nextSellerId = Member::generateSellerId();
    //     $prefill      = [];

    //     return view('members.index', compact('members', 'allMembers', 'products', 'cities', 'nextSellerId', 'prefill'));
    // }


public function index(Request $request)
{
    Log::info('Members index started', [
        'request' => $request->all()
    ]);

    $start = microtime(true);

    $query = Member::query();

    if ($request->filled('sponsor_id')) {
        Log::info('Applying sponsor filter', [
            'sponsor_id' => $request->sponsor_id
        ]);

        $query->where('sponsor_id', $request->sponsor_id);
    }

    if ($request->filled('search')) {
        Log::info('Applying search filter', [
            'search' => $request->search
        ]);

        $query->where(function ($q) use ($request) {
            $q->where('first_name', 'like', '%' . $request->search . '%')
              ->orWhere('last_name', 'like', '%' . $request->search . '%')
              ->orWhere('contact', 'like', '%' . $request->search . '%');
        });
    }

    Log::info('Before paginate', [
        'elapsed_seconds' => round(microtime(true) - $start, 2)
    ]);

    $members = $query->latest()->paginate(5);

    Log::info('Members pagination completed', [
        'count' => $members->count(),
        'elapsed_seconds' => round(microtime(true) - $start, 2)
    ]);

    Log::info('Loading all active members');

    $allMembers = Member::where('is_active', 1)
        ->orderBy('first_name')
        ->get(['id', 'seller_id', 'first_name', 'last_name']);

    Log::info('All active members loaded', [
        'count' => $allMembers->count(),
        'elapsed_seconds' => round(microtime(true) - $start, 2)
    ]);

    Log::info('Loading products');

    $products = Product::orderBy('product_name')->get();

    Log::info('Products loaded', [
        'count' => $products->count(),
        'elapsed_seconds' => round(microtime(true) - $start, 2)
    ]);

    Log::info('Loading cities');

    $cities = City::orderBy('name')->get();

    Log::info('Cities loaded', [
        'count' => $cities->count(),
        'elapsed_seconds' => round(microtime(true) - $start, 2)
    ]);

    Log::info('Generating seller id');

    $nextSellerId = Member::generateSellerId();

    Log::info('Seller id generated', [
        'seller_id' => $nextSellerId,
        'elapsed_seconds' => round(microtime(true) - $start, 2)
    ]);

    $prefill = [];

    Log::info('Returning members index view', [
        'total_elapsed_seconds' => round(microtime(true) - $start, 2)
    ]);

    return view('members.index', compact(
        'members',
        'allMembers',
        'products',
        'cities',
        'nextSellerId',
        'prefill'
    ));
}

    public function create(Request $request)
    {
        $query = Member::query();

        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('first_name', 'like', '%'.$request->search.'%')
                  ->orWhere('last_name',  'like', '%'.$request->search.'%')
                  ->orWhere('contact',    'like', '%'.$request->search.'%');
            });
        }

        $members      = $query->latest()->paginate(5);
        $allMembers   = Member::where('is_active', 1)
                              ->orderBy('first_name')
                              ->get(['id', 'seller_id', 'first_name', 'last_name']);
        $products     = Product::orderBy('product_name')->get();
        $cities       = City::orderBy('name')->get();
        $nextSellerId = Member::generateSellerId();

        $prefill = [
            'first_name' => $request->name,
            'aadhar_no'  => $request->aadhar,
            'contact'    => $request->mobile,
            'sponsor_id' => $request->sponsor_id,
            'position'   => $request->leg ?? 'left',
        ];

        return view('members.index', compact('members', 'allMembers', 'products', 'cities', 'nextSellerId', 'prefill'));
    }

    public function store(Request $request)
    {
        // ✅ Duplicate check — Aadhar
        if ($request->filled('aadhar_no')) {
            $exists = Member::where('aadhar_no', $request->aadhar_no)->first();
            if ($exists) {
                return back()->withErrors(['aadhar_no' => 'Aadhar already registered: ' . $exists->seller_id])->withInput();
            }
        }

        // ✅ Duplicate check — Mobile
        if ($request->filled('contact')) {
            $exists = Member::where('contact', $request->contact)->first();
            if ($exists) {
                return back()->withErrors(['contact' => 'Mobile already registered: ' . $exists->seller_id])->withInput();
            }
        }

        $validator = \Validator::make($request->all(), [
            'first_name'      => 'required|string|max:100',
            'last_name'       => 'nullable|string|max:100',
            'contact'         => 'required|digits:10',
            'address'         => 'nullable|string',
            'aadhar_no'       => 'nullable|digits:12',
            'city'            => 'nullable',
            'date_of_joining' => 'required|date',
            'sponsor_id'      => 'nullable|string',
            'sponsor_leg'     => 'nullable|in:left,right',
            'password'        => 'required|min:4|confirmed',
            'profile_image'   => 'nullable|image|max:2048',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $leg  = $request->sponsor_leg ?? 'left';
        $data = $request->except(['password', 'password_confirmation', 'profile_image', 'sponsor_leg']);
        $data['password']  = Hash::make($request->password);
        $data['position']  = $leg;
        $data['seller_id'] = Member::generateSellerId();

        // ✅ Product — from_order hai toh SubOrder se dhundo
        $product       = null;
        $subOrderAmount = 0;

        if ($request->from_order && $request->filled('aadhar_no')) {
            $subOrder = \App\Models\SubOrder::where('aadhar', $request->aadhar_no)
                            ->latest()
                            ->first();
            if ($subOrder && $subOrder->product_id) {
                $product        = Product::find($subOrder->product_id);
                $subOrderAmount = floatval($subOrder->amount ?? 0);
            }
        }

        // ✅ Fallback — direct member add par Product::first()
        $product = $product ?? Product::first();

        if ($product) {
            $data['product_id'] = $product->id;
        }

        if ($request->filled('sponsor_id')) {
            $data['parent_id'] = $this->findAvailableParent($request->sponsor_id, $leg);
        }

        if ($request->hasFile('profile_image')) {
            $data['profile_image'] = $request->file('profile_image')->store('profiles', 'public');
        }

        $member = Member::create($data);

        \App\Models\User::updateOrCreate(
            ['seller_id' => $data['seller_id']],
            [
                'name'     => $request->first_name . ' ' . $request->last_name,
                'password' => Hash::make($request->password),
            ]
        );

        // // ✅ Order sirf non-from_order par banao
        // if ($product && !$request->from_order) {
        //     \App\Models\Order::create([
        //         'order_no'       => 'ORD-' . strtoupper(uniqid()),
        //         'member_id'      => $member->id,
        //         'product_id'     => $product->id,
        //         'order_date'     => $member->date_of_joining,
        //         'order_product'  => $product->product_name,
        //         'order_quantity' => 1,
        //         'order_value'    => $product->product_price,
        //         'total_value'    => $product->product_price,
        //         'total_bv'       => round(($product->business_value / 100) * $product->product_price, 2),
        //         'status'         => 'delivered',
        //     ]);
        // }

        // ✅ Commission — sahi product aur sahi amount se
        // if ($product) {
        //     $member->distributeCommission($product, 1, $subOrderAmount);
        // }

        if ($request->from_order) {
            return redirect()->back()->with('success', 'Member registered successfully!');
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
        // ensure numeric values
        $directIncome    = (float) ($member->direct_commission ?? 0);
        $level1Income    = (float) ($member->level1_commission ?? 0);
        $level2Income    = (float) ($member->level2_commission ?? 0);
        $newJoineeIncome = (float) ($member->new_joinee_income ?? 0);

        $totalCommission = $directIncome + $level1Income + $level2Income + $newJoineeIncome;

        // team income is level1 + level2
        $teamIncome = $level1Income + $level2Income;

        $teamIncomePercentage = $totalCommission ? round(($teamIncome / $totalCommission) * 100, 2) : 0;
        $sponsorIncomePercentage = $totalCommission ? round(($directIncome / $totalCommission) * 100, 2) : 0;

        return view('members.seller-profile', compact('member', 'totalCommission', 'teamIncomePercentage', 'sponsorIncomePercentage'));
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
            'password'        => 'nullable|min:4|confirmed',
        ]);

        $data = $request->except(['profile_image', '_method', '_token', 'password', 'password_confirmation']);

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

    public function nextSellerId()
    {
        return response()->json([
            'seller_id' => Member::generateSellerId(),
        ]);
    }
}