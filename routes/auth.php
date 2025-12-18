<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthenticatedSessionController;

Route::middleware('guest')->group(function () {
    Route::view('/', 'welcome')->name('welcome');
    Route::get('/auth/{role}', [AuthenticatedSessionController::class, 'showLoginForm'])->name('login.role');
    Route::post('/login', [AuthenticatedSessionController::class, 'store'])->name('login.post');
});

Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout')->middleware('auth');