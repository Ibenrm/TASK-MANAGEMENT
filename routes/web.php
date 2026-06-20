<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

Route::get('/', function () {
    return view('login');
})->middleware('guest')->name('login');

Route::get('/auth/google', [AuthController::class, 'redirectToGoogle'])->middleware('guest');
Route::get('/auth/google/callback', [AuthController::class, 'handleGoogleCallback'])->middleware('guest');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware('auth')->name('dashboard');
