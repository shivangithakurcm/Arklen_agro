<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthApiController;
use App\Http\Controllers\Api\MemberApiController;
use App\Http\Controllers\Api\ProductApiController;
use App\Http\Controllers\Api\OrderApiController;

// ── Public Routes ─────────────────────────────────────
Route::post('/login',           [AuthApiController::class, 'login']);
Route::post('/forgot-password', [AuthApiController::class, 'forgotPassword']);
Route::post('/verify-otp',      [AuthApiController::class, 'verifyOtp']);
Route::post('/reset-password',  [AuthApiController::class, 'resetPassword']);

// ── Protected Routes ──────────────────────────────────
Route::middleware('auth:sanctum')->group(function () {

    // Auth
    Route::post('/logout', [AuthApiController::class, 'logout']);
    Route::get('/me',      [AuthApiController::class, 'me']);

    // Members
    Route::get('/members',                   [MemberApiController::class, 'index']);
    Route::post('/members',                  [MemberApiController::class, 'store']);
    Route::get('/members/{member}',          [MemberApiController::class, 'show']);
    Route::put('/members/{member}',          [MemberApiController::class, 'update']);
    Route::delete('/members/{member}',       [MemberApiController::class, 'destroy']);

    // Member Action (income/bv update)
    Route::get('/members/{member}/action',   [MemberApiController::class, 'action']);
    Route::put('/members/{member}/action',   [MemberApiController::class, 'actionUpdate']);

    // Member Profile
    Route::get('/members/{member}/profile',  [MemberApiController::class, 'profile']);

    // Member Team
    Route::get('/members/{member}/team',     [MemberApiController::class, 'team']);

    // ── Products ──────────────────────────────────────
    Route::get('/products',              [ProductApiController::class, 'index']);
    Route::post('/products',             [ProductApiController::class, 'store']);
    Route::get('/products/{product}',    [ProductApiController::class, 'show']);
    Route::put('/products/{product}',    [ProductApiController::class, 'update']);
    Route::delete('/products/{product}', [ProductApiController::class, 'destroy']);

    // ── Orders ────────────────────────────────────────
    Route::get('/orders',                [OrderApiController::class, 'index']);
    Route::post('/orders',               [OrderApiController::class, 'store']);
    Route::get('/orders/{order}',        [OrderApiController::class, 'show']);
    Route::put('/orders/{order}',        [OrderApiController::class, 'update']);
    Route::delete('/orders/{order}',     [OrderApiController::class, 'destroy']);
    Route::get('/orders/{order}/items',  [OrderApiController::class, 'items']);
    Route::put('/orders/{order}/status', [OrderApiController::class, 'updateStatus']);
    // Orders Action Routes
    Route::get('/orders/{order}/action',  [OrderApiController::class, 'action']);
Route::put('/orders/{order}/action',  [OrderApiController::class, 'actionUpdate']);

});