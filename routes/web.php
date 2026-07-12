<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\MenuController;
use App\Http\Controllers\Admin\ItemController;
use App\Http\Controllers\Admin\QrCodeController;
use Inertia\Inertia;


Route::inertia('/', 'Welcome')->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::inertia('dashboard', 'Dashboard')->name('dashboard');
});

Route::get('/menu', [MenuController::class, 'index'])->name('menu');

// --- Routes admin : protégées par la session (Breeze fournit déjà 'auth') ---
// Le middleware 'auth' redirige vers /login si l'utilisateur n'est pas connecté.
Route::middleware(['auth', 'verified'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/items', [ItemController::class, 'index'])->name('items.index');
    Route::post('/items', [ItemController::class, 'store'])->name('items.store');
    Route::post('/items/{item}', [ItemController::class, 'update'])->name('items.update'); // + _method=PUT
    Route::delete('/items/{item}', [ItemController::class, 'destroy'])->name('items.destroy');

Route::get('/qr-code', [QrCodeController::class, 'show'])->name('qr-code');
Route::get('/qr-code/image', [QrCodeController::class, 'image'])->name('qr-code.image');

});


require __DIR__.'/settings.php';
