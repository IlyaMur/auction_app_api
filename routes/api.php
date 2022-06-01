<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DesignController;
use App\Http\Controllers\User\MeController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\User\UserController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Teams\TeamsController;
use App\Http\Controllers\User\SettingsController;
use App\Http\Controllers\Designs\UploadController;
use App\Http\Controllers\Designs\CommentController;
use App\Http\Controllers\Auth\VerificationController;
use App\Http\Controllers\Teams\InvitationsController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Chats\ChatsController;

/**
 * Public routes
 */

// Get current auth user
Route::get('me', [MeController::class, 'getMe']);

// Search designs
Route::controller(DesignController::class)->group(function () {
    Route::get('designs', 'index');
    Route::get('designs/{id}', 'findDesign');
    Route::get('designs/slug/{slug}', 'findBySlug');
    Route::get('users/{id}/designs', 'getForUser');
    Route::get('teams/{id}/designs', 'getForTeam');
    Route::get('search/designs', 'search');
});

// Search users (designers)
Route::controller(UserController::class)->group(function () {
    Route::get('users', 'index');
    Route::get('user/{username}', 'findByUsername');
    Route::get('search/designers', 'search');
});

Route::get('teams/slug/{slug}', [TeamsController::class, 'findBySlug']);

/**
 * Routes for auth users only
 */

Route::group(['middleware' => 'auth:api'], function () {
    Route::post('logout', [LoginController::class, 'logout']);

    // User's settings
    Route::controller(SettingsController::class)->group(function () {
        Route::put('settings/profile', 'updateProfile');
        Route::put('settings/password', 'updatePassword');
    });

    // Design upload
    Route::post('designs', [UploadController::class, 'upload']);

    // Comments
    Route::controller(CommentController::class)->group(function () {
        Route::post('designs/{id}/comments', 'store');
        Route::put('comments/{id}', 'update');
        Route::delete('comments/{id}', 'destroy');
    });

    // Designs
    Route::controller(DesignController::class)->group(function () {
        Route::post('designs/{id}/like', 'like');
        Route::get('designs/{id}/liked', 'checkIfUserHasLiked');
        Route::put('designs/{design}', 'update');
        Route::delete('designs/{design}', 'destroy');
    });

    // Teams
    Route::controller(TeamsController::class)->group(function () {
        Route::post('teams', 'store');
        Route::get('teams/{id}', 'findById');
        Route::get('teams/', 'index');
        Route::get('users/teams/', 'fetchUserTeams');
        Route::put('teams/{id}', 'update');
        Route::delete('teams/{id}', 'destroy');
        Route::delete('teams/{teamId}/users/{userId}', 'removeFromTeam');
    });

    // Invitations
    Route::controller(InvitationsController::class)->group(function () {
        Route::post('invitations/{teamId}', 'invite');
        Route::post('invitations/{id}/resend', 'resend');
        Route::post('invitations/{id}/respond', 'respond');
        Route::delete('invitations/{id}', 'destroy');
    });

    // Chats
    Route::controller(ChatsController::class)->group(function () {
        Route::get('chats', 'getUserChats');
        Route::post('chats', 'sendMessage');
        Route::get('chats/{id}/messages', 'getChatMessages');
        Route::put('chats/{id}/markAsRead', 'markAsRead');
        Route::delete('messages/{id}', 'destroyMessage');
    });
});

/**
 * Routes for guests
 */

Route::group(['middleware' => 'guest:api'], function () {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [LoginController::class, 'login']);

    // New user's verification
    Route::controller(VerificationController::class)->group(function () {
        Route::post('verification/verify/{user}', 'verify')
            ->name('verification.verify');
        Route::post('verification/resend', 'resend');
    });

    // Password
    Route::post('password/email', [ForgotPasswordController::class, 'sendResetLinkEmail']);
    Route::post('password/reset', [ResetPasswordController::class, 'reset'])
        ->name('password.reset');
});
