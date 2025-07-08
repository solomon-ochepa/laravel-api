<?php

use Illuminate\Support\Facades\Route;
use Modules\Role\App\Http\Controllers\RoleController;

Route::middleware(['auth:api'])->prefix('v1')->group(function () {
    Route::get('roles', [RoleController::class, 'index'])->name('role.index');
});
