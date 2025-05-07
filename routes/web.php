<?php
use Illuminate\Support\Facades\Route;
use MyOrg\GovBrAuth\Http\Controllers\AuthController;

Route::prefix('auth/govbr')->group(function(){
    Route::get('redirect', [AuthController::class, 'redirectToProvider'])
         ->name('govbr.login');

    Route::get('callback', [AuthController::class, 'handleProviderCallback'])
         ->name('govbr.callback');

    Route::post('logout', [AuthController::class, 'logout'])
         ->name('govbr.logout');
});
