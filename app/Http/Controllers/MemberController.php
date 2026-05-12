<?php

namespace App\Http\Controllers;

use App\Models\Member;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class MemberController extends Controller
{
    public function index(Request $request)
    {
        $query = Member::query();
        if ($request->filled('sponsor_id')) {
            $query->where('sponsor_id', $request->sponsor_id);
        }
        $members = $query->latest()->paginate(5);
        return view('members.index', compact('members'));
    }

    public function store(Request $request)
{
    $request->validate([
        'seller_id'       => 'required|string|unique:members,seller_id',
        'first_name'      => 'required|string|max:100',
        'last_name'       => 'required|string|max:100',
       'contact'         => 'required|digits:10',        // exactly 10 digits
        'address'         => 'required|string',
        'aadhar_no'       => 'nullable|digits:12',   
        'date_of_joining' => 'required|date',
        'sponsor_id'      => 'nullable|string',
        'sponsor_leg'     => 'nullable|in:left,right',
        'password'        => 'required|min:4|confirmed',
        'profile_image'   => 'nullable|image|max:2048',
    ]);

    $data             = $request->except(['password', 'password_confirmation', 'profile_image', 'sponsor_leg']);
    $data['password'] = Hash::make($request->password);
    $data['position'] = $request->sponsor_leg ?? 'left';

    if ($request->hasFile('profile_image')) {
        $data['profile_image'] = $request->file('profile_image')->store('profiles', 'public');
    }

    Member::create($data);

    // Users table mein bhi add karo
    \App\Models\User::create([
        'name'      => $request->first_name . ' ' . $request->last_name,
        'seller_id' => $request->seller_id,
        'password'  => Hash::make($request->password),
    ]);

    return redirect()->route('dashboard')->with('success', 'Member added successfully!');
}
    public function show(Member $member)
    {
        return view('members.seller-profile', compact('member'));
    }

    public function edit(Member $member)
    {
        return view('members.edit', compact('member'));
    }

    public function update(Request $request, Member $member)
    {
        $request->validate([
            'first_name'      => 'required|string|max:100',
            'last_name'       => 'required|string|max:100',
            'contact'         => 'required|digits:10',        // exactly 10 digits
        'address'         => 'required|string',
        'aadhar_no'       => 'nullable|digits:12',   
            'date_of_joining' => 'required|date',
            'sponsor_id'      => 'nullable|string',
            'position'        => 'nullable|in:left,right',
            'is_active'       => 'nullable|boolean',
            'profile_image'   => 'nullable|image|max:2048',
        ]);

        $data = $request->except(['profile_image', '_method', '_token']);

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
            'team_income', 'pay_income', 'total_income', 'team_bv'
        ]));

        return redirect()->route('dashboard')->with('success', 'Updated successfully!');
    }
}