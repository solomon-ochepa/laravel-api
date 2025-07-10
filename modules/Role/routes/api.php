<?php

use Illuminate\Support\Facades\Route;
use Modules\Role\App\Http\Controllers\Api\RoleController;

Route::prefix('v1')->group(function () {
    Route::apiResource('roles', RoleController::class);
});
