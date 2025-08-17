<?php

use Illuminate\Support\Facades\Route;
use Modules\OAuth\App\Http\Controllers\OAuthController;

Route::get('oauth/redirect', [OAuthController::class, 'redirect'])->name('oauth.redirect');
Route::get('auth/callback', [OAuthController::class, 'callback'])->name('auth.callback');

require_once 'passport.php';
