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
Route::post('/tugas', [TaskController::class, 'store'])->middleware('auth')->name('tasks.store');
Route::put('/tugas/{task}', [TaskController::class, 'update'])->middleware('auth')->name('tasks.update');
Route::post('/tugas/reorder', [TaskController::class, 'reorder'])->middleware('auth')->name('tasks.reorder');
Route::get('/activity-history', function () {
    // 1. Ambil semua data dari database dari yang terlama (ascending)
    $dbLogs = \App\Models\ActivityLog::with(['user', 'task'])->orderBy('created_at', 'asc')->get();
    
    // 2. Inisialisasi struktur data Stack bawaan PHP (LIFO)
    $stack = new \SplStack();
    
    // 3. Masukkan data log ke dalam stack
    foreach ($dbLogs as $log) {
        $stack->push($log);
    }
    
    // 4. Keluarkan data dari stack ke array biasa (akan otomatis berurutan dari yang terbaru karena sifat LIFO)
    $stackArray = [];
    foreach ($stack as $item) {
        $stackArray[] = $item;
    }
    
    // 5. Buat paginasi manual untuk data array
    $currentPage = request()->get('page', 1);
    $perPage = 30;
    $currentItems = array_slice($stackArray, ($currentPage - 1) * $perPage, $perPage);
    
    $logs = new \Illuminate\Pagination\LengthAwarePaginator(
        $currentItems,
        count($stackArray),
        $perPage,
        $currentPage,
        ['path' => request()->url(), 'query' => request()->query()]
    );

    return view('activities', compact('logs'));
})->middleware('auth')->name('activity.history');
