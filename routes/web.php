<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\InviteController; //  ADD THIS
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {

    //  Profile routes (default)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    //  Invite system routes (NEW)
    Route::get('/invites', [InviteController::class, 'index'])->name('invites.index');
    Route::post('/invites/create', [InviteController::class, 'create'])->name('invites.create');
});

require __DIR__.'/auth.php';