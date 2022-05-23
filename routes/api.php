<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DesignController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\VerificationController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\User\MeController;
use App\Http\Controllers\User\SettingsController;
use App\Http\Controllers\User\UserController;
use App\Http\Controllers\Designs\UploadController;

/**
 * Public routes
 */

// Get current auth user
Route::get('me', [MeController::class, 'getMe']);

Route::get('designs', [DesignController::class, 'index']);
Route::get('users', [UserController::class, 'index']);

/**
 * Routes for auth users only
 */

Route::group([
    'middleware' => ['auth:api'],
], function () {
    Route::post('logout', [LoginController::class, 'logout']);

    Route::put('settings/profile', [SettingsController::class, 'updateProfile']);
    Route::put('settings/password', [SettingsController::class, 'updatePassword']);

    // Design resource operations
    Route::post('designs', [UploadController::class, 'upload']);
    Route::put('designs/{design}', [DesignController::class, 'update']);
    Route::delete('designs/{design}', [DesignController::class, 'destroy']);
});

/**
 * Routes for guests
 */

Route::group([
    'middleware' => ['guest:api'],
], function () {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [LoginController::class, 'login']);

    Route::post('verification/verify/{user}', [VerificationController::class, 'verify'])->name('verification.verify');
    Route::post('verification/resend', [VerificationController::class, 'resend']);

    Route::post('password/email', [ForgotPasswordController::class, 'sendResetLinkEmail']);
    Route::post('password/reset', [ResetPasswordController::class, 'reset'])->name('password.reset');
});
