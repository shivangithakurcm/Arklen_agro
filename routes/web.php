<?php

use App\Http\Controllers\OrderController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\MemberController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CityController;

// ── Auth ──────────────────────────────────────────────
Route::get('/', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');

// ── Protected ──────────────────────────────────────────
Route::middleware('auth')->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

   
    // ── Members ────────────────────────────────────────
    Route::get('members/create', [MemberController::class, 'create'])->name('members.create');
    Route::get('/members',                      [MemberController::class, 'index'])->name('members.index');
    Route::get('/members/next-seller-id',       [MemberController::class, 'nextSellerId'])->name('members.next-seller-id');
    Route::post('/members',                     [MemberController::class, 'store'])->name('members.store');
    Route::get('/members/{member}',             [MemberController::class, 'show'])->name('members.show');
    Route::get('/members/{member}/edit',        [MemberController::class, 'edit'])->name('members.edit');
    Route::put('/members/{member}',             [MemberController::class, 'update'])->name('members.update');
    Route::get('/members/{member}/action',      [MemberController::class, 'action'])->name('members.action');
    Route::put('/members/{member}/action',      [MemberController::class, 'actionUpdate'])->name('members.action.update');
    Route::put('/members/{member}/override',    [MemberController::class, 'adminOverride'])->name('members.admin-override');
    Route::get('/members/{member}/recalculate', [MemberController::class, 'recalculate'])->name('members.recalculate');

    // ── Products ───────────────────────────────────────
    Route::get('/products',                [ProductController::class, 'index'])->name('products.index');
    Route::post('/products',               [ProductController::class, 'store'])->name('products.store');
    Route::get('/products/{product}/edit', [ProductController::class, 'edit'])->name('products.edit');
    Route::put('/products/{product}',      [ProductController::class, 'update'])->name('products.update');
    Route::delete('/products/{product}',   [ProductController::class, 'destroy'])->name('products.destroy');

    // ── Tree ──────────────────────────────────────────
    Route::get('/tree', [MemberController::class, 'tree'])->name('tree');
    Route::resource('cities', CityController::class)->only(['index', 'store', 'destroy']);

    // ── Orders ────────────────────────────────────────
    Route::get('orders/create',             [OrderController::class, 'create'])->name('orders.create');
    Route::get('/orders',                   [OrderController::class, 'index'])->name('orders.index');
    Route::post('/orders',                  [OrderController::class, 'store'])->name('orders.store');
    Route::get('/orders/{order}',           [OrderController::class, 'show'])->name('orders.show');
    Route::get('/orders/{order}/edit',      [OrderController::class, 'edit'])->name('orders.edit');
    Route::put('/orders/{order}',           [OrderController::class, 'update'])->name('orders.update');
    Route::get('/orders/{order}/action',    [OrderController::class, 'action'])->name('orders.action');
    Route::put('/orders/{order}/action',    [OrderController::class, 'actionUpdate'])->name('orders.action.update');
    Route::patch('orders/{order}/toggle',   [OrderController::class, 'toggle'])->name('orders.toggle');
    Route::get('/orders/{order}/invoice',   [OrderController::class, 'invoice'])->name('orders.invoice');

});