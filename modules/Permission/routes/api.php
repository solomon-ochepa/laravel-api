<?php

use Illuminate\Support\Facades\Route;
use Modules\Permission\App\Http\Controllers\PermissionController;

Route::middleware(['auth:api'])->prefix('v1')->group(function () {
    Route::get('permissions', [PermissionController::class, 'index'])->name('permission.index');
});
