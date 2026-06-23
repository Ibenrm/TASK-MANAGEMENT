<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\TaskController;

Route::get('/', function () {
    return view('login');
})->middleware('guest')->name('login');

Route::get('/auth/google', [AuthController::class, 'redirectToGoogle'])->middleware('guest');
Route::get('/auth/google/callback', [AuthController::class, 'handleGoogleCallback'])->middleware('guest');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::get('/dashboard', [DashboardController::class, 'index'])->middleware('auth')->name('dashboard');

Route::get('/tugas', [TaskController::class, 'index'])->middleware('auth')->name('tugas');

Route::get('/activity-history', function () {
    return view('activities');
})->middleware('auth')->name('activity.history');
