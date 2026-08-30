<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\MenuController;
use App\Http\Controllers\Admin\ItemController;
use App\Http\Controllers\Admin\QrCodeController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\SettingsController;



Route::redirect('/', '/login');

Route::middleware(['auth', 'verified'])->get('/dashboard', function () {
    return redirect()->route('admin.items.index');})->name('dashboard');

    Route::get('/menu', [MenuController::class, 'index'])->name('menu');

Route::middleware(['auth', 'verified'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/items/{category?}', [ItemController::class, 'index'])->name('items.index');
    Route::post('/items', [ItemController::class, 'store'])->name('items.store');
    Route::post('/items/{item}/update', [ItemController::class, 'update'])->name('items.update');
    Route::delete('/items/{item}', [ItemController::class, 'destroy'])->name('items.destroy');

    Route::get('/qr-code', [QrCodeController::class, 'show'])->name('qr-code');
    Route::get('/qr-code/image', [QrCodeController::class, 'image'])->name('qr-code.image');
    Route::put('/categories/{category}/availability', [CategoryController::class, 'updateAvailability'])->name('categories.availability');

    Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
    Route::post('/categories', [CategoryController::class, 'store'])->name('categories.store');
    Route::put('/categories/{category}', [CategoryController::class, 'update'])->name('categories.update');
    Route::delete('/categories/{category}', [CategoryController::class, 'destroy'])->name('categories.destroy');
});

require __DIR__.'/settings.php';