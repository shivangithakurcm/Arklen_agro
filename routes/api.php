<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthApiController;
use App\Http\Controllers\Api\MemberApiController;

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
    Route::get('/members',                    [MemberApiController::class, 'index']);
    Route::post('/members',                   [MemberApiController::class, 'store']);
    Route::get('/members/{member}',           [MemberApiController::class, 'show']);
    Route::put('/members/{member}',           [MemberApiController::class, 'update']);
    Route::delete('/members/{member}',        [MemberApiController::class, 'destroy']);

    // Member Action (income/bv update)
    Route::get('/members/{member}/action',    [MemberApiController::class, 'action']);
    Route::put('/members/{member}/action',    [MemberApiController::class, 'actionUpdate']);

    // Member Profile
    Route::get('/members/{member}/profile',   [MemberApiController::class, 'profile']);

    // Member Team
    Route::get('/members/{member}/team',      [MemberApiController::class, 'team']);

});