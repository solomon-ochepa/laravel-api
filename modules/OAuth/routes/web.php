<?php

use Illuminate\Support\Facades\Route;
use Modules\OAuth\App\Http\Controllers\OAuthController;

Route::get('oauth', [OAuthController::class, 'oauth'])->name('oauth');
Route::get('auth/callback', [OAuthController::class, 'callback'])->name('oauth.callback');

require_once 'passport.php';
