<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\RekamMedisController;
use SebastianBergmann\CodeCoverage\Report\Html\Dashboard;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.submit');

Route::get('/dashboard', [DashboardController::class, 'index'])->middleware(['auth', 'role'])->name('dashboard');
Route::get('/rekam-medis/{id}', [RekamMedisController::class, 'show'])->middleware(['auth', 'role'])->name('rekam_medis.show');
