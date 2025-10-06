<?php

use Illuminate\Support\Facades\Route;
use Modules\User\App\Http\Controllers\Api\UserController;

Route::middleware(['auth:api'])->prefix('v1')->group(function () {
    Route::apiResource('users', UserController::class)->names('users');

    // Trash system
    Route::prefix('users')->as('users.')->group(function () {
        Route::patch('{user}/restore', [UserController::class, 'restore'])->name('restore');
        Route::delete('{user}/force', [UserController::class, 'force_delete'])->name('delete.force');
    });
});
