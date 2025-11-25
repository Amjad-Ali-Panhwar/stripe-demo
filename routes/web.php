<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\StripeController;

Route::get('/', function () {
    return view('welcome');
});


Route::get('/checkout', [StripeController::class, 'showCheckoutPage'])->name('stripe.checkout');
Route::post('/create-checkout-session', [StripeController::class, 'createCheckoutSession'])->name('stripe.create');
Route::get('/success', [StripeController::class, 'success'])->name('stripe.success');
Route::get('/cancel', [StripeController::class, 'cancel'])->name('stripe.cancel');

// Webhook endpoint — POST only
Route::post('/webhook/stripe', [StripeController::class, 'handleWebhook'])->name('stripe.webhook');