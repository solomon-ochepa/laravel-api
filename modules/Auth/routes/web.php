<?php

use Illuminate\Support\Facades\Route;
use Modules\Auth\App\Http\Controllers\AuthController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('auths', [AuthController::class, 'index'])->name('auth.index');
});
