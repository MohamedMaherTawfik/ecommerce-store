<?php

use App\Http\Controllers\api\admin\BrandController;
use App\Http\Controllers\api\admin\CategoreyController;
use App\Http\Controllers\api\admin\ProductController;
use App\Http\Controllers\api\admin\UserController;
use App\Http\Controllers\api\auth\AuthController;
use App\Http\Controllers\api\auth\GoogleAuthController;
use App\Http\Middleware\AdminMiddleware;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {

    Route::prefix('users')->group(function () {
        Route::post('register', [AuthController::class, 'register'])->middleware(['throttle:3,1', 'guest']);
        Route::post('login', [AuthController::class, 'login'])->middleware(['throttle:7,1', 'guest']);
        Route::post('forgot-password', [AuthController::class, 'forgotPassword'])
            ->middleware(['throttle:5,1', 'guest']);
        Route::post('reset-password', [AuthController::class, 'resetPassword'])
            ->middleware(['throttle:5,1', 'guest']);

        Route::get('/google-login', [GoogleAuthController::class, 'googleLogin'])->middleware('guest');
        Route::get('/google-callback', [GoogleAuthController::class, 'googleCallback'])->middleware('guest');

        Route::middleware(['auth:sanctum', 'throttle:15,1'])->group(function () {
            Route::get('profile', [AuthController::class, 'profile']);
            Route::put('profile', [AuthController::class, 'updateProfile']);
            Route::put('password', [AuthController::class, 'updatePassword']);
        });
    });

    Route::prefix('users')->middleware(['auth:sanctum', AdminMiddleware::class, 'throttle:30,1'])->group(function () {
        Route::get('/', [UserController::class, 'index']);
        Route::get('/all/get', [UserController::class, 'all']);
        Route::get('/User/count', [UserController::class, 'count']);
        Route::get('/{id}', [UserController::class, 'show']);
        Route::post('/create', [UserController::class, 'create']);
        Route::post('/{id}', [UserController::class, 'update']);
        Route::delete('/{id}', [UserController::class, 'destroy']);
    });

    Route::prefix('brands')->middleware(['auth:sanctum', AdminMiddleware::class, 'throttle:30,1'])->group(function () {
        Route::get('/', [BrandController::class, 'index']);
        Route::get('/all/brands', [BrandController::class, 'all']);
        Route::get('/brand/count', [BrandController::class, 'count']);
        Route::get('/{id}', [BrandController::class, 'show']);
        Route::get('/{id}/products', [BrandController::class, 'products']);
        Route::post('/create', [BrandController::class, 'create']);
        Route::post('/{id}', [BrandController::class, 'update']);
        Route::delete('/{id}', [BrandController::class, 'destroy']);
    });

    Route::prefix('categories')->middleware(['auth:sanctum', AdminMiddleware::class, 'throttle:30,1'])->group(function () {
        Route::get('/', [CategoreyController::class, 'index']);
        Route::get('/all/categories', [CategoreyController::class, 'all']);
        Route::get('/category/count', [CategoreyController::class, 'count']);
        Route::get('/{id}', [CategoreyController::class, 'show']);
        Route::get('/{id}/products', [CategoreyController::class, 'products']);
        Route::post('/create', [CategoreyController::class, 'create']);
        Route::post('/{id}', [CategoreyController::class, 'update']);
        Route::delete('/{id}', [CategoreyController::class, 'destroy']);
    });

    Route::prefix('products')->middleware(['auth:sanctum', AdminMiddleware::class, 'throttle:30,1'])->group(function () {
        Route::get('/', [ProductController::class, 'index']);
        Route::get('/products/count', [ProductController::class, 'count']);
        Route::get('/{id}', [ProductController::class, 'show']);
        Route::post('/create', [ProductController::class, 'create']);
        Route::post('/{id}', [ProductController::class, 'update']);
        Route::delete('/{id}', [ProductController::class, 'destroy']);
    });
});
