<?php

use App\Http\Controllers\Api\AuthController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Authentication Routes (No CSRF required)
|--------------------------------------------------------------------------
| These routes are for API/Swagger authentication without CSRF tokens
| They return JSON responses with tokens instead of redirects
*/

Route::post('/login', [AuthController::class, 'login'])->name('api.login');

// No /register here either, and for a stronger reason than on the web side:
// this endpoint sits outside CSRF and outside auth, and handed back a working
// API token on success. Anyone who could reach the host could have minted
// themselves an account and a token. Accounts come from Settings > Users.

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('api.logout');
    Route::get('/user', [AuthController::class, 'user'])->name('api.user');

    // Future API routes
});
