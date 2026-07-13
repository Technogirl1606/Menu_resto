<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\MenuController;
use App\Http\Controllers\Admin\ItemController;
use App\Http\Controllers\Admin\QrCodeController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Admin\ProfileController;
use App\Http\Controllers\Admin\PasswordController;

// Redirige la page d'accueil vers le menu, en attendant une vraie landing page.
// Le nom 'home' est conservé : les pages d'authentification de Breeze en dépendent.
Route::redirect('/', '/menu')->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::inertia('dashboard', 'Dashboard')->name('dashboard');
});

// Page publique du menu : ce que le client voit après avoir scanné le QR code
Route::get('/menu', [MenuController::class, 'index'])->name('menu');

// --- Routes admin : protégées par la session (Breeze fournit déjà 'auth') ---
// Le middleware 'auth' redirige vers /login si l'utilisateur n'est pas connecté.
Route::middleware(['auth', 'verified'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/items/{category?}', [ItemController::class, 'index'])->name('items.index');
    Route::post('/items', [ItemController::class, 'store'])->name('items.store');
    Route::post('/items/{item}/update', [ItemController::class, 'update'])->name('items.update');
    Route::delete('/items/{item}', [ItemController::class, 'destroy'])->name('items.destroy');

    Route::get('/qr-code', [QrCodeController::class, 'show'])->name('qr-code');
    Route::get('/qr-code/image', [QrCodeController::class, 'image'])->name('qr-code.image');
});

require __DIR__.'/settings.php';