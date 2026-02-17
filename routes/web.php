<?php

use App\Http\Controllers\UserController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\MediaController;
use App\Http\Controllers\DisplayController;
use App\Http\Controllers\DisplayMediaController;
use App\Http\Controllers\DisplayClientController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;


// Auth routes
Auth::routes();

// Authenticated routes
Route::middleware(['auth'])->group(function () {

    // Dashboard
    Route::get('/', [App\Http\Controllers\DashboardController::class, 'index'])->name('dashboard');

    // Master Data - Departemen
    Route::resource('master-data/departemen', App\Http\Controllers\MasterData\DepartemenController::class);

    // Master Data - Food
    Route::resource('master-data/food', App\Http\Controllers\MasterData\FoodController::class)->except(['create', 'edit']);
    Route::post('master-data/food/{uuid}/stock', [App\Http\Controllers\MasterData\FoodController::class, 'addStock'])->name('food.addStock');

    // Redirect 'root' to dashboard (for Laravel UI compatibility)
    Route::redirect('/root', '/')->name('root');

    //User Management
    Route::resource('users', UserController::class);

    // Role Management
    Route::resource('roles', RoleController::class);

    // Permission Management
    Route::resource('permissions', PermissionController::class);

    // Saldo Management
    Route::get('/saldo', [App\Http\Controllers\Saldo\SaldoController::class, 'index'])->name('saldo.index');
    Route::get('/saldo/{uuid}', [App\Http\Controllers\Saldo\SaldoController::class, 'show'])->name('saldo.show');
    Route::post('/saldo', [App\Http\Controllers\Saldo\SaldoController::class, 'store'])->name('saldo.store');

    // Event Management
    Route::resource('event', App\Http\Controllers\Event\EventController::class);
    Route::post('event/{uuid}/status', [App\Http\Controllers\Event\EventController::class, 'updateStatus'])->name('event.updateStatus');
});

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
