<?php

use Illuminate\Support\Facades\Route;
use Modules\Role\App\Http\Controllers\RoleController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('roles', [RoleController::class, 'index'])->name('role.index');
});
