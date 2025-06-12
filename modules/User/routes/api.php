<?php

use Illuminate\Support\Facades\Route;
use Modules\User\App\Http\Controllers\UserController;

Route::middleware(['auth:api'])->prefix('v1')->group(function () {
    Route::get('users', [UserController::class, 'index'])->name('user.index');
});
