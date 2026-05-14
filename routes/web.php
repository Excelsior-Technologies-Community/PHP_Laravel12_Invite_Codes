<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\InviteController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return redirect()->route('invites.index');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {

    /*
    |--------------------------------------------------------------------------
    | Profile Routes
    |--------------------------------------------------------------------------
    */

    Route::get('/profile', [ProfileController::class, 'edit'])
        ->name('profile.edit');

    Route::patch('/profile', [ProfileController::class, 'update'])
        ->name('profile.update');

    Route::delete('/profile', [ProfileController::class, 'destroy'])
        ->name('profile.destroy');

    /*
    |--------------------------------------------------------------------------
    | Invite Routes
    |--------------------------------------------------------------------------
    */

    // Show all invite codes
    Route::get('/invites', [InviteController::class, 'index'])
        ->name('invites.index');

    // Create invite code
    Route::post('/invites/create', [InviteController::class, 'create'])
        ->name('invites.create');

    // Delete invite code
    Route::delete('/invites/{id}', [InviteController::class, 'destroy'])
        ->name('invites.destroy');
});

require __DIR__.'/auth.php';