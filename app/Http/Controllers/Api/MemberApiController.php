<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Member;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class MemberApiController extends Controller
{
    // Get all members
    public function index()
    {
        $members = Member::latest()->get();

        return response()->json([
            'status' => true,
            'data'   => $members,
        ]);
    }

    // Store member
    public function store(Request $request)
    {
        $request->validate([
            'first_name'      => 'required|string|max:100',
            'last_name'       => 'required|string|max:100',
            'contact'         => 'required|string|max:15',
            'seller_id'       => 'required|string|unique:members,seller_id',
            'sponsor_id'      => Member::exists() ? 'required|exists:members,seller_id' : 'nullable',
            'position'        => 'nullable|in:left,right',
            'address'         => 'required|string',
            'aadhar_no'       => 'nullable|string|max:12',
            'date_of_joining' => 'required|date',
            'password'        => 'required|min:4|confirmed',
        ]);

        $member = Member::create([
            'first_name'      => $request->first_name,
            'last_name'       => $request->last_name,
            'contact'         => $request->contact,
            'seller_id'       => $request->seller_id,
            'sponsor_id'      => $request->sponsor_id,
            'position'        => $request->position ?? 'left',
            'address'         => $request->address,
            'aadhar_no'       => $request->aadhar_no,
            'date_of_joining' => $request->date_of_joining,
            'password'        => Hash::make($request->password),
        ]);

        return response()->json([
            'status'  => true,
            'message' => 'Member created successfully',
            'data'    => $member,
        ], 201);
    }

    // Show single member
    public function show($id)
    {
        $member = Member::find($id);

        if (!$member) {
            return response()->json([
                'status'  => false,
                'message' => 'Member not found',
            ], 404);
        }

        return response()->json([
            'status' => true,
            'data'   => $member,
        ]);
    }

    // Update member
    public function update(Request $request, $id)
    {
        $member = Member::find($id);

        if (!$member) {
            return response()->json([
                'status'  => false,
                'message' => 'Member not found',
            ], 404);
        }

        $request->validate([
            'first_name'      => 'sometimes|required|string|max:100',
            'last_name'       => 'sometimes|required|string|max:100',
            'contact'         => 'sometimes|required|string|max:15',
            'sponsor_id'      => 'nullable|exists:members,seller_id',
            'position'        => 'nullable|in:left,right',
            'address'         => 'sometimes|required|string',
            'aadhar_no'       => 'nullable|string|max:12',
            'date_of_joining' => 'sometimes|required|date',
            'is_active'       => 'nullable|boolean',
        ]);

        $member->update($request->only([
            'first_name', 'last_name', 'contact', 'sponsor_id',
            'position', 'address', 'aadhar_no', 'date_of_joining', 'is_active',
        ]));

        return response()->json([
            'status'  => true,
            'message' => 'Member updated successfully',
            'data'    => $member->fresh(),
        ]);
    }

    // Delete member
    public function destroy($id)
    {
        $member = Member::find($id);

        if (!$member) {
            return response()->json([
                'status'  => false,
                'message' => 'Member not found',
            ], 404);
        }

        $member->delete();

        return response()->json([
            'status'  => true,
            'message' => 'Member deleted successfully',
        ]);
    }

    // Member profile with team info
    public function profile(Member $member)
    {
        return response()->json([
            'status' => true,
            'data'   => [
                'member'         => $member,
                'sponsor'        => $member->sponsor(),
                'left_members'   => $member->leftMembers(),
                'right_members'  => $member->rightMembers(),
            ],
        ]);
    }

    // Member team
    public function team(Member $member)
    {
        return response()->json([
            'status' => true,
            'data'   => [
                'left'  => $member->leftMembers(),
                'right' => $member->rightMembers(),
            ],
        ]);
    }

    // Member action (income/bv)
    public function action(Member $member)
    {
        return response()->json([
            'status' => true,
            'data'   => $member->only([
                'bv_left', 'bv_right', 'sponsor_income', 'direct_sponsor_income',
                'team_income', 'pay_income', 'total_income', 'balance', 'team_bv',
            ]),
        ]);
    }

    // Update member action (income/bv)
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
            'balance'               => 'nullable|numeric|min:0',
            'team_bv'               => 'nullable|numeric|min:0',
        ]);

        $member->update($request->only([
            'bv_left', 'bv_right', 'sponsor_income', 'direct_sponsor_income',
            'team_income', 'pay_income', 'total_income', 'balance', 'team_bv',
        ]));

        return response()->json([
            'status'  => true,
            'message' => 'Updated successfully',
            'data'    => $member->fresh(),
        ]);
    }
}