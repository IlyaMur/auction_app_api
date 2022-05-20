<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\User\MeController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\VerificationController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\Auth\ForgotPasswordController;

// Public routes

Route::get('me', [MeController::class, 'getMe']);

// Routes for auth users only
Route::group([
    'middleware' => ['auth:api'],
], function () {
    Route::post('logout', [LoginController::class, 'logout']);
});

// Routes for guests
Route::group([
    'middleware' => ['guest:api'],
], function () {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('verification/verify/{user}', [VerificationController::class, 'verify'])->name('verification.verify');
    Route::post('verification/resend', [VerificationController::class, 'resend']);
    Route::post('login', [LoginController::class, 'login']);

    Route::post('password/email', [ForgotPasswordController::class, 'sendResetLinkEmail']);
    Route::post('password/reset', [ResetPasswordController::class, 'reset'])->name('password.reset');
});
