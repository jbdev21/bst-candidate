<?php

use App\Http\Controllers\QuoteController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', fn () => inertia('Welcome'))->name('home');
Route::get('/demo', [QuoteController::class, 'demo'])->middleware(['auth']);
Route::get('/get-token', function () {
    return Auth::user()->createToken(uniqid());
});

Route::get('dashboard', fn () => Inertia::render('Dashboard'))
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
