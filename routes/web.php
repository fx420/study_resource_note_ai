<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\ProfileController;

Route::get('/', function () {
    return view('index');
})->name('index');

/* ---------------------- AUTHENTICATION ---------------------- */
Route::get('/login',    [AuthController::class,'showLogin'])->name('login');
Route::post('/login',   [AuthController::class,'login']);

Route::get('/register', [AuthController::class,'showRegister'])->name('register');
Route::post('/register',[AuthController::class,'register']);

Route::post('/logout',  [AuthController::class,'logout'])->name('logout');

/* ---------------------- PROFILE CONTROLLER ---------------------- */
Route::middleware(['auth'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile.index');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
});

/* ---------------------- SEARCH CONTROLLER ---------------------- */
Route::get('/search', [SearchController::class, 'index'])->name('search');

/* ---------------------- LIBRARY CONTROLLER ---------------------- */
Route::get('/library', function () {
    $notes = [
        [
            'id'      => 1,
            'title'   => 'Linear Algebra Summary',
            'content' => "Eigenvalues and eigenvectors are…\nApplications include…",
            'date'    => '2025-04-16',
        ],
        [
            'id'      => 2,
            'title'   => 'Calculus Cheat Sheet',
            'content' => "Derivatives: f'(x) = lim…\nIntegrals: ∫ f(x) dx = …",
            'date'    => '2025-04-15',
        ],
    ];

    return view('library', compact('notes'));
})->name('library');
