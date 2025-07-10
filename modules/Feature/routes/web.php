<?php

use Illuminate\Support\Facades\Route;
use Modules\Feature\App\Http\Controllers\FeatureController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('features', [FeatureController::class, 'index'])->name('feature.index');
});
