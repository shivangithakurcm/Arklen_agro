<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

 public function login(Request $request)
{
    $request->validate([
        'user_id'  => 'required|string',
        'password' => 'required|string',
    ]);

    $user = User::where('seller_id', $request->user_id)->first();

    if (!$user || !Hash::check($request->password, $user->password)) {
        return back()
            ->withErrors(['user_id' => 'Invalid Seller ID or Password.'])
            ->withInput();
    }

    Auth::guard('web')->login($user, true); // true = remember me
    $request->session()->regenerate(); // session regenerate
    return redirect()->intended('/dashboard');
}
    public function logout(Request $request)
    {
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        return redirect()->route('login');
    }
}