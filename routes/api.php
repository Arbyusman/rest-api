<?php

use App\Http\Controllers\Api\Auth\AuthController;
use App\Http\Controllers\Api\TaskController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::get('/health', fn () => response()->json(['status' => 'API is running'], 200));

    Route::prefix('auth')->controller(AuthController::class)->group(function () {
        Route::post('login', 'login')->name('login');
        Route::post('logout', 'logout')->middleware('auth:sanctum')->name('logout');
    });

    Route::post('users', [UserController::class, 'store']);

    Route::prefix('task')
        ->middleware('auth:sanctum')
        ->controller(TaskController::class)->group(function () {
            Route::post('', 'store');
            Route::get('/{id}', 'show');
            Route::put('/{id}', 'update');
            Route::patch('/{id}/status', 'status');
            Route::patch('/{id}/report', 'report');
        });
});
