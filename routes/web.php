<?php

use App\Http\Controllers\Dashboard\ArticleController;
use App\Http\Controllers\Dashboard\ArticleCommentController;
use App\Http\Controllers\Dashboard\ArticleRatingController;
use App\Http\Controllers\Dashboard\CommunityPostController;
use App\Http\Controllers\Dashboard\CommunityPostCommentController;
use App\Http\Controllers\Dashboard\DashboardController;
use App\Http\Controllers\Dashboard\ReportController;
use App\Http\Controllers\Dashboard\UserController;
use App\Http\Controllers\Dashboard\EraController;
use App\Http\Controllers\Dashboard\GovernorateController;
use App\Http\Controllers\Dashboard\CertifiedResearcherController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Articles Management
    Route::resource('dashboard/articles', ArticleController::class);
    Route::delete('dashboard/articles/images/{image}', [ArticleController::class, 'deleteImage'])->name('articles.images.destroy');

    // Comments Management
    Route::get('dashboard/comments', [ArticleCommentController::class, 'index'])->name('comments.index');
    Route::post('dashboard/comments/{id}/approve', [ArticleCommentController::class, 'approve'])->name('comments.approve');
    Route::delete('dashboard/comments/{id}', [ArticleCommentController::class, 'destroy'])->name('comments.destroy');

    // Ratings Management

    Route::prefix('dashboard/ratings')->name('ratings.')->group(function () {
        Route::get('/', [ArticleRatingController::class, 'index'])->name('index');
        Route::get('/statistics', [ArticleRatingController::class, 'statistics'])->name('statistics');
        Route::get('/{id}', [ArticleRatingController::class, 'show'])->name('show');
        Route::post('/{id}/approve', [ArticleRatingController::class, 'approve'])->name('approve');
        Route::delete('/{id}', [ArticleRatingController::class, 'destroy'])->name('destroy');
        Route::post('/bulk-approve', [ArticleRatingController::class, 'bulkApprove'])->name('bulk-approve');
        Route::post('/bulk-delete', [ArticleRatingController::class, 'bulkDelete'])->name('bulk-delete');
    });


    // Community Posts Management
    Route::get('dashboard/community-posts', [CommunityPostController::class, 'index'])->name('community_posts.index');
    Route::delete('dashboard/community-posts/{id}', [CommunityPostController::class, 'destroy'])->name('community_posts.destroy');

    // Community Comments Management
    Route::get('dashboard/community-comments', [CommunityPostCommentController::class, 'index'])->name('community_comments.index');
    Route::delete('dashboard/community-comments/{id}', [CommunityPostCommentController::class, 'destroy'])->name('community_comments.destroy');

    // Users Management
    Route::resource('dashboard/users', UserController::class)->only(['index', 'edit', 'update', 'destroy']);

    // Certified Researchers Management
    Route::get('dashboard/certified-researchers', [CertifiedResearcherController::class, 'index'])->name('certified_researchers.index');
    Route::get('dashboard/certified-researchers/{certifiedResearcher}', [CertifiedResearcherController::class, 'show'])->name('certified_researchers.show');
    Route::post('dashboard/certified-researchers/{certifiedResearcher}/approve', [CertifiedResearcherController::class, 'approve'])->name('certified_researchers.approve');
    Route::post('dashboard/certified-researchers/{certifiedResearcher}/reject', [CertifiedResearcherController::class, 'reject'])->name('certified_researchers.reject');
    Route::delete('dashboard/certified-researchers/{certifiedResearcher}', [CertifiedResearcherController::class, 'destroy'])->name('certified_researchers.destroy');

    // Eras and Governorates Management
    Route::resource('dashboard/eras', EraController::class)->only(['index', 'create', 'store', 'edit', 'update', 'destroy']);
    Route::resource('dashboard/governorates', GovernorateController::class)->only(['index', 'create', 'store', 'edit', 'update', 'destroy']);

    // 🏛️ HERITAGE REPORTS ROUTES - CLEAN VERSION
    Route::prefix('dashboard/reports')->name('reports.')->group(function () {
        // Main Heritage Dashboard
        Route::get('/', [ReportController::class, 'heritageDashboard'])->name('dashboard');

        // Geographic Analysis
        Route::get('/geographic', [ReportController::class, 'geographicAnalysis'])->name('geographic');

        // Historical Timeline
        Route::get('/timeline', [ReportController::class, 'timelineReport'])->name('timeline');

        // Research Community Report
        Route::get('/research-community', [ReportController::class, 'researchCommunityReport'])->name('research-community');

        // Content Quality Report
        Route::get('/content-quality', [ReportController::class, 'contentQualityReport'])->name('content-quality');

        // Tourism Potential Report
        Route::get('/tourism-potential', [ReportController::class, 'tourismPotentialReport'])->name('tourism-potential');

        // Platform Growth Report
        Route::get('/growth', [ReportController::class, 'growthReport'])->name('growth');

        // Export Reports
        Route::get('/export/{reportType}', [ReportController::class, 'exportReport'])->name('export');
    });
});

require __DIR__ . '/auth.php';
