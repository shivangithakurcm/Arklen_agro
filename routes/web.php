<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\MemberController;
use Illuminate\Support\Facades\Route;

// ── Auth ────────────────────────────────────────────────
Route::get('/',       [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');

// ── Protected ────────────────────────────────────────────
Route::middleware('auth')->group(function () {

    Route::get('/dashboard', function () {
        $query = \App\Models\Member::query();
        if (request('sponsor_id')) {
            $query->where('sponsor_id', request('sponsor_id'));
        }
        $members = $query->latest()->paginate(15);
        return view('dashboard', compact('members'));
    })->name('dashboard');

    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::get('/members',                 [MemberController::class, 'index'])->name('members.index');
    Route::post('/members',                [MemberController::class, 'store'])->name('members.store');
    Route::get('/members/{member}',        [MemberController::class, 'show'])->name('members.show');
    Route::get('/members/{member}/edit',   [MemberController::class, 'edit'])->name('members.edit');
    Route::put('/members/{member}',        [MemberController::class, 'update'])->name('members.update');
    Route::get('/members/{member}/action', [MemberController::class, 'action'])->name('members.action');
    Route::put('/members/{member}/action', [MemberController::class, 'actionUpdate'])->name('members.action.update');
});