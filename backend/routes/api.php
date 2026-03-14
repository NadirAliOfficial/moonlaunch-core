<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\ApiController;
use App\Http\Controllers\WebhookController;
use App\Http\Controllers\TokenController;
use App\Http\Controllers\WalletController;
use App\Http\Controllers\TradeController;

Route::post('/signup', [ApiController::class, 'signup']);
Route::post('/verify-otp', [ApiController::class, 'verifyOtp']);
Route::post('/request_login', [ApiController::class, 'login']);
Route::post('/verify-login-otp', [ApiController::class, 'verifyLoginOtp']);
Route::post('/login', [ApiController::class, 'simpleLogin']);
Route::post('/edit_profile', [ApiController::class, 'editProfile']);
Route::post('/send-otp', [ApiController::class, 'sendUserOtp']);
Route::post('/resend_otp', [ApiController::class, 'sendOtp']);
Route::post('/reset_password', [ApiController::class, 'resetPassword']);
Route::post('/change_password', [ApiController::class, 'changePassword']);
Route::post('/webhook/pancakeswap', [WebhookController::class, 'handlePairCreated']);
Route::get('/new-launches', [TokenController::class, 'newLaunches']);
Route::get('/token/{address}', [TokenController::class, 'getTokenDetail']);
Route::get('/wallet/balance', [WalletController::class, 'getBalance']);
Route::post('/wallet/send', [TradeController::class, 'send']);
Route::post('/trade/buy', [TradeController::class, 'buy']);
Route::post('/trade/sell', [TradeController::class, 'sell']);