<?php

use Illuminate\Support\Facades\Route;
use Modules\Auth\App\Http\Controllers\APi\AuthController;

Route::middleware(['auth:api'])->prefix('v1')->group(function () {
    Route::post('/register', [AuthController::class, 'register'])->name('register');
    Route::post('/login', [AuthController::class, 'login'])->name('login');
});
