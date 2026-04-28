<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LocaleController;
use App\Http\Controllers\WatchedCurrencyController;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('home');
Route::post('locale/{locale}', LocaleController::class)->name('locale.update');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::post('watched-currencies', [WatchedCurrencyController::class, 'store'])->name('watched-currencies.store');
    Route::delete('watched-currencies/{watchedCurrency}', [WatchedCurrencyController::class, 'destroy'])->name('watched-currencies.destroy');
});

require __DIR__.'/settings.php';
