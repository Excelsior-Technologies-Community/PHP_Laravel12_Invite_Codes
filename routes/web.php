<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\InviteController;
use App\Http\Controllers\Auth\RegisteredUserController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return redirect()->route('invites.index');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/invites', [InviteController::class, 'index'])->name('invites.index');
    Route::post('/invites/create', [InviteController::class, 'create'])->name('invites.create');
    Route::get('/invites/send-email/{id}', [InviteController::class, 'sendEmail'])->name('invites.send-email');
    Route::delete('/invites/{id}', [InviteController::class, 'destroy'])->name('invites.destroy');
    Route::get('/invites/referrals', [InviteController::class, 'referralDashboard'])->name('invites.referrals');
    Route::post('/invites/validate', [InviteController::class, 'validateCode'])->name('invites.validate');
});

// Registration with invite code
Route::get('/register/{invite?}', [RegisteredUserController::class, 'create'])->name('register');
Route::post('/register', [RegisteredUserController::class, 'store']);

require __DIR__.'/auth.php';