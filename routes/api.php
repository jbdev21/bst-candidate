<?php

use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\QuoteController;
use App\Http\Controllers\WebhookController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum'])->group(function () {
    Route::post('/quote', [QuoteController::class, 'store'])->name('api.quote.store');
    Route::post('/checkout', [CheckoutController::class, 'store'])->name('api.checkout.store');
});

// Mock fulfillment inventory endpoints
Route::get('/mock-fulfillment/availability/{sku}', [CheckoutController::class, 'mockAvailability'])->name('api.fulfillment.availability.show');
Route::post('/mock-fulfillment/availability', [CheckoutController::class, 'setMockAvailability'])->name('api.fulfillment.availability.store');

// Payment webhooks
Route::post('/webhooks/payments', [WebhookController::class, 'payments'])->name('api.webhooks.payments');
