<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\MenuController;
use App\Http\Controllers\Admin\ItemController;
use App\Http\Controllers\Admin\QrCodeController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\SettingsController;


Route::redirect('/', '/menu');

Route::middleware(['auth', 'verified'])->get('/dashboard', function () {
    return redirect()->route('admin.items.index');
})->name('dashboard');

Route::get('/menu', [MenuController::class, 'index'])->name('menu');

Route::middleware(['auth', 'verified'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/items/{category?}', [ItemController::class, 'index'])->name('items.index');
    Route::post('/items', [ItemController::class, 'store'])->name('items.store');
    Route::post('/items/{item}/update', [ItemController::class, 'update'])->name('items.update');
    Route::delete('/items/{item}', [ItemController::class, 'destroy'])->name('items.destroy');

    Route::get('/qr-code', [QrCodeController::class, 'show'])->name('qr-code');
    Route::get('/qr-code/image', [QrCodeController::class, 'image'])->name('qr-code.image');
});

require __DIR__.'/settings.php';