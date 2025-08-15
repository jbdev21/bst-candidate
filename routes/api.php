<?php

use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\QuoteController;
use App\Http\Controllers\WebhookController;
use Illuminate\Support\Facades\Route;

Route::post('/quote', [QuoteController::class, 'store']);
Route::post('/checkout', [CheckoutController::class, 'store']);

// Mock fulfillment inventory endpoints
Route::get('/mock-fulfillment/availability/{sku}', [CheckoutController::class, 'mockAvailability']);
Route::post('/mock-fulfillment/availability', [CheckoutController::class, 'setMockAvailability']);

// Payment webhooks
Route::post('/webhooks/payments', [WebhookController::class, 'payments']);
