<?php

use App\Http\Controllers\Admin\DoctorController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Doctor\ProfileController as DoctorProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Patient\DoctorController as PatientDoctorController;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::delete('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);
    Route::get('/doctors', [PatientDoctorController::class, 'index']);

    //our prefix add /api/admin for first part in every url
    Route::prefix('admin')->group(function () {
        Route::post('/doctors', [DoctorController::class, 'store']);
        Route::get('/doctors', [DoctorController::class, 'index']);
        Route::get('/doctors/{doctor}', [DoctorController::class, 'show']);
        Route::patch('/doctors/{doctor}/status', [DoctorController::class, 'updateStatus']);
        Route::patch('/doctors/{doctor}',[DoctorController::class, 'update']);
    });

    Route::prefix('doctor')->group(function () {
        Route::get('/profile', [DoctorProfileController::class, 'show']);
        Route::patch('/profile', [DoctorProfileController::class, 'update']);
    });
});
