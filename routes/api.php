<?php

use App\Http\Controllers\Api\ArticleController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CommunityPostController;
use App\Http\Controllers\Api\GovernorateController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\ContactUsController;
use App\Http\Controllers\Api\CertifiedResearcherController;
use Illuminate\Support\Facades\Route;

// Public routes
Route::prefix('auth')->group(function () {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);
    Route::post('forgot-password', [AuthController::class, 'forgotPassword']);
});

// Public contact us route (no authentication required)
Route::post('contact-us', [ContactUsController::class, 'store'])
    ->middleware('throttle:3,1'); // 3 requests per minute



// Authenticated routes
Route::middleware('auth:sanctum')->group(function () {
    // Auth routes
    Route::prefix('auth')->group(function () {
        Route::post('logout', [AuthController::class, 'logout']);
        Route::get('profile', [AuthController::class, 'profile']);
        Route::put('profile', [AuthController::class, 'updateProfile']);
        Route::post('refresh-token', [AuthController::class, 'refreshToken']);
        Route::delete('delete-account', [AuthController::class, 'deleteAccount']);
        Route::post('reset-password', [AuthController::class, 'resetPassword']);
    });

    // Article routes
    Route::prefix('articles')->group(function () {
        Route::get('/', [ArticleController::class, 'index']);
        Route::get('{id}', [ArticleController::class, 'show']);
        Route::post('{id}/comment', [ArticleController::class, 'comment']);
        Route::post('{id}/rate', [ArticleController::class, 'rate']);
    });

    // Community post routes
    Route::prefix('community-posts')->group(function () {
        Route::get('/', [CommunityPostController::class, 'index']);
        Route::post('/', [CommunityPostController::class, 'store']);
        Route::get('{id}', [CommunityPostController::class, 'show']);
        Route::post('{id}/comment', [CommunityPostController::class, 'comment']);
    });

    // Certified researcher routes
    Route::prefix('certified-researcher')->group(function () {
        Route::post('apply', [CertifiedResearcherController::class, 'apply']);
        Route::get('status', [CertifiedResearcherController::class, 'status']);
    });
//  governorate routes
    Route::prefix('governorates')->group(function () {
        Route::get('/', [GovernorateController::class, 'index']);
        Route::get('{id}', [GovernorateController::class, 'show']);
    });
    // Admin-only routes
    Route::middleware('role:admin')->group(function () {
        Route::prefix('admin')->group(function () {
            // Users management
            Route::get('users', [UserController::class, 'index']);
            Route::get('users/{id}', [UserController::class, 'show']);
            Route::put('users/{id}', [UserController::class, 'update']);
            Route::delete('users/{id}', [UserController::class, 'destroy']);
            Route::post('users/{id}/change-role', [UserController::class, 'changeRole']);

            // Certified researcher applications management
            Route::prefix('certified-researcher')->group(function () {
                Route::get('applications', [CertifiedResearcherController::class, 'index']);
                Route::post('applications/{id}/approve', [CertifiedResearcherController::class, 'approve']);
                Route::post('applications/{id}/reject', [CertifiedResearcherController::class, 'reject']);
            });
        });
    });

    // Researcher routes
    Route::middleware('role:researcher')->group(function () {
        Route::prefix('researcher')->group(function () {
            Route::get('dashboard', function () {
                return response()->json(['message' => 'Researcher dashboard']);
            });
        });
    });
});

