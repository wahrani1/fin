<?php

use App\Http\Controllers\Api\ArticleController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CommunityPostController;
use App\Http\Controllers\Api\GovernorateController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\ContactUsController;
use App\Http\Controllers\Api\CertifiedResearcherController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\SearchController;

// Public routes
Route::prefix('auth')->group(function () {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);
    Route::post('forgot-password', [AuthController::class, 'forgotPassword']);
});

// Public contact us route (no authentication required)
Route::post('contact-us', [ContactUsController::class, 'store'])
    ->middleware('throttle:3,1'); // 3 requests per minute


// PUBLIC SEARCH ROUTES (no authentication required)
Route::prefix('search')->group(function () {
    // Global search across all content types
    Route::get('/', [SearchController::class, 'globalSearch']);

    // Get filter options for frontend dropdowns
    Route::get('/filters', [SearchController::class, 'getFilterOptions']);

    // Get trending/popular content
    Route::get('/trending', [SearchController::class, 'getTrending']);
});


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

    // Community post and comments routes
    Route::prefix('community-posts')->group(function () {
        Route::get('/', [CommunityPostController::class, 'index']);
        Route::post('/', [CommunityPostController::class, 'store']);
        Route::get('{id}', [CommunityPostController::class, 'show']);
        Route::put('{id}', [CommunityPostController::class, 'update']);           // Edit post
        Route::delete('{id}', [CommunityPostController::class, 'destroy']);       // Delete post
        Route::post('{id}/comment', [CommunityPostController::class, 'comment']);
        Route::put('comments/{commentId}', [CommunityPostController::class, 'updateComment']);    // Edit comment
        Route::delete('comments/{commentId}', [CommunityPostController::class, 'destroyComment']); // Delete comment
    });

    // Certified researcher routes
    Route::prefix('certified-researcher')->group(function () {
        Route::post('apply', [CertifiedResearcherController::class, 'apply']);
        Route::get('status', [CertifiedResearcherController::class, 'status']);
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
// AUTHENTICATED SEARCH ROUTES

    Route::prefix('search')->group(function () {
        // Advanced article search with all filters
        Route::get('/articles', [SearchController::class, 'searchArticles']);

        // Governorate search and filtering
        Route::get('/governorates', [SearchController::class, 'searchGovernorates']);
    });

    // Enhanced governorate routes with articles pagination
    Route::prefix('governorates')->group(function () {
        Route::get('/', [GovernorateController::class, 'index']);
        Route::get('{id}', [GovernorateController::class, 'show']);
        Route::get('{id}/articles', [GovernorateController::class, 'articles']);
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

