<?php

use App\Http\Controllers\PaymentController;
use App\Http\Controllers\TranzakWebhookController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});
// web.php
Route::get('/pay/{order}', [PaymentController::class, 'show']);
Route::get('/payment/success', [PaymentController::class, 'success']);
Route::get('/payment/cancel', [PaymentController::class, 'failed']);
Route::post('/tranzak/webhook', [TranzakWebhookController::class, 'handle']);
