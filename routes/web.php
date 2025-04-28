<?php

use App\Models\Note;
use Illuminate\Support\Facades\Auth;

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\LibraryController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\PromptTemplateController;
use App\Http\Controllers\LogController;
use App\Http\Controllers\ContentModerationController;
use App\Http\Controllers\LearnedDataController;
use App\Http\Controllers\FileUploadController;
use App\Http\Controllers\ChatController;

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

/* ---------------------- UPLOAD CONTROLLER ---------------------- */
Route::get('/upload', [FileUploadController::class, 'showForm'])->name('upload.form');
Route::post('/upload', [FileUploadController::class, 'upload'])->name('upload.submit');

/* ---------------------- CHAT CONTROLLER ---------------------- */
Route::post('/chat/submit', [ChatController::class,'submit'])
     ->name('chat.submit'); 

/* ---------------------- LIBRARY CONTROLLER ---------------------- */
Route::middleware('auth')->get('/library', function () {
    $notes = Note::where('user_id', Auth::id())
                 ->orderBy('created_at', 'desc')
                 ->get();

    return view('library', compact('notes'));
})->name('library');

/* ---------------------- ADMIN CONTROLLER ---------------------- */
Route::middleware(['auth','can:admin'])->prefix('admin')->name('admin.')->group(function(){
    Route::get('/', [AdminController::class,'dashboard'])->name('dashboard');

    Route::resource('prompt-templates', PromptTemplateController::class);
    Route::resource('logs',            LogController::class)->only(['index','show','destroy']);
    Route::resource('moderation',     ContentModerationController::class)->only(['index','update']);
    Route::resource('learned-data',   LearnedDataController::class);
});