<?php

use Illuminate\Support\Facades\Route;
use Modules\Permission\App\Http\Controllers\PermissionController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('permissions', [PermissionController::class, 'index'])->name('permission.index');
});
