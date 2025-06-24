<?php

use Illuminate\Support\Facades\Route;
use Modules\Auth\App\Http\Controllers\AuthController;

Route::middleware(['auth:api'])->prefix('v1')->group(function () {
    Route::get('auths', [AuthController::class, 'index'])->name('auth.index');
});
