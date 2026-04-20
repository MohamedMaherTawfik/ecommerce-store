<?php

use App\Http\Controllers\api\auth\GoogleAuthController;
use Illuminate\Support\Facades\Route;
Route::get('/v1/users/google-login', [GoogleAuthController::class, 'googleLogin']);
Route::get('/v1/users/google-callback', [GoogleAuthController::class, 'googleCallback']);

Route::get('/{any}', function () {
    return view('index');
})->where('any', '.*');