<?php

use App\Http\Controllers\Admin\DoctorController;
use App\Http\Controllers\Auth\AuthController;
use Illuminate\Support\Facades\Route;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::delete('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);

    //our prefix add /api/admin for first part in every url
    Route::prefix('admin')->group(function () {
        Route::post('/doctors', [DoctorController::class, 'store']);
    });
});
