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

Route::get('designs', [DesignController::class, 'index']);
Route::get('designs/{id}', [DesignController::class, 'findDesign']);

Route::get('users', [UserController::class, 'index']);

Route::get('teams/slug/{slug}', [TeamsController::class, 'findBySlug']);

// Search Designs
Route::get('search/designs', [DesignController::class, 'search']);
Route::get('search/designers', [UserController::class, 'search']);

/**
 * Routes for auth users only
 */
Route::group([
    'middleware' => ['auth:api'],
], function () {
    // Auth
    Route::post('logout', [LoginController::class, 'logout']);

    // User's settings
    Route::put('settings/profile', [SettingsController::class, 'updateProfile']);
    Route::put('settings/password', [SettingsController::class, 'updatePassword']);

    // Design resource operations
    Route::post('designs', [UploadController::class, 'upload']);
    Route::put('designs/{design}', [DesignController::class, 'update']);
    Route::delete('designs/{design}', [DesignController::class, 'destroy']);

    // Comments
    Route::post('designs/{id}/comments', [CommentController::class, 'store']);
    Route::put('comments/{id}', [CommentController::class, 'update']);
    Route::delete('comments/{id}', [CommentController::class, 'destroy']);

    // Likes and Unlikes
    Route::post('designs/{id}/like', [DesignController::class, 'like']);
    Route::get('designs/{id}/liked', [DesignController::class, 'checkIfUserHasLiked']);

    // Teams
    Route::post('teams', [TeamsController::class, 'store']);
    Route::get('teams/{id}', [TeamsController::class, 'findById']);
    Route::get('teams/', [TeamsController::class, 'index']);
    Route::get('users/teams/', [TeamsController::class, 'fetchUserTeams']);
    Route::put('teams/{id}', [TeamsController::class, 'update']);
    Route::delete('teams/{id}', [TeamsController::class, 'destroy']);
    Route::delete('teams/{teamId}/users/{userId}', [TeamsController::class, 'removeFromTeam']);

    // Invitations
    Route::post('invitations/{teamId}', [InvitationsController::class, 'invite']);
    Route::post('invitations/{id}/resend', [InvitationsController::class, 'resend']);
    Route::post('invitations/{id}/respond', [InvitationsController::class, 'respond']);
    Route::delete('invitations/{id}', [InvitationsController::class, 'destroy']);

    // Chats
    Route::get('chats', [ChatsController::class, 'getUserChats']);
    Route::post('chats', [ChatsController::class, 'sendMessage']);
    Route::get('chats/{id}/messages', [ChatsController::class, 'getChatMessages']);
    Route::put('chats/{id}/markAsRead', [ChatsController::class, 'markAsRead']);
    Route::delete('messages/{id}', [ChatsController::class, 'destroyMessage']);
});

/**
 * Routes for guests
 */

Route::group([
    'middleware' => ['guest:api'],
], function () {
    // Auth
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [LoginController::class, 'login']);

    // New user's verification
    Route::post('verification/verify/{user}', [VerificationController::class, 'verify'])->name('verification.verify');
    Route::post('verification/resend', [VerificationController::class, 'resend']);

    // Password
    Route::post('password/email', [ForgotPasswordController::class, 'sendResetLinkEmail']);
    Route::post('password/reset', [ResetPasswordController::class, 'reset'])->name('password.reset');
});
