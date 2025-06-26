<?php

use App\Http\Controllers\Api\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

Route::group(['prefix' => 'v1'], function () {
    Route::get('/health', function () {
        return response()->json(['status' => 'API is running'], 200);
    });


    Route::post('users', [UserController::class, 'store']);

    // Route::resource('/users', UserController::class)->only(['store']);
});
