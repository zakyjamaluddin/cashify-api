<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\WalletController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\TransactionController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\WalletInvitationController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::post('/register', [AuthController::class, 'register']);
Route::post('/verify-otp', [AuthController::class, 'verifyOtp']);
Route::post('/resend-otp', [AuthController::class, 'resendOtp']);

Route::post('/login', [AuthController::class, 'login']);
Route::get('/documentation', function () {
    return response()->view('api_documentation');
});

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    Route::post('/logout', [AuthController::class, 'logout']);

    Route::apiResource('wallets', WalletController::class);
    Route::get('wallets/{wallet}/users', [WalletController::class, 'users']);
    Route::post('wallets/{wallet}/invitations', [WalletInvitationController::class, 'store']);
    Route::get('invitations', [WalletInvitationController::class, 'index']);
    Route::post('invitations/{invitation}/accept', [WalletInvitationController::class, 'accept']);
    Route::post('invitations/{invitation}/decline', [WalletInvitationController::class, 'decline']);
    Route::apiResource('categories', CategoryController::class);
    Route::apiResource('transactions', TransactionController::class);
    Route::apiResource('users', UserController::class);
});
