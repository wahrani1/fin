<?php

use App\Http\Controllers\Dashboard\ArticleController;
use App\Http\Controllers\Dashboard\ArticleCommentController;
use App\Http\Controllers\Dashboard\ArticleRatingController;
use App\Http\Controllers\Dashboard\CommunityPostController;
use App\Http\Controllers\Dashboard\CommunityPostCommentController;
use App\Http\Controllers\Dashboard\DashboardController;
use App\Http\Controllers\Dashboard\UserController;
use App\Http\Controllers\Dashboard\EraController;
use App\Http\Controllers\Dashboard\GovernorateController;
use App\Http\Controllers\Dashboard\CertifiedResearcherController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::resource('dashboard/articles', ArticleController::class);
    Route::delete('dashboard/articles/images/{image}', [ArticleController::class, 'deleteImage'])->name('articles.images.destroy');

    Route::get('dashboard/comments', [ArticleCommentController::class, 'index'])->name('comments.index');
    Route::post('dashboard/comments/{id}/approve', [ArticleCommentController::class, 'approve'])->name('comments.approve');
    Route::delete('dashboard/comments/{id}', [ArticleCommentController::class, 'destroy'])->name('comments.destroy');

    Route::get('dashboard/ratings', [ArticleRatingController::class, 'index'])->name('ratings.index');
    Route::post('dashboard/ratings/{id}/approve', [ArticleRatingController::class, 'approve'])->name('ratings.approve');
    Route::delete('dashboard/ratings/{id}', [ArticleRatingController::class, 'destroy'])->name('ratings.destroy');

    Route::get('dashboard/community-posts', [CommunityPostController::class, 'index'])->name('community_posts.index');
    Route::delete('dashboard/community-posts/{id}', [CommunityPostController::class, 'destroy'])->name('community_posts.destroy');

    Route::get('dashboard/community-comments', [CommunityPostCommentController::class, 'index'])->name('community_comments.index');
    Route::delete('dashboard/community-comments/{id}', [CommunityPostCommentController::class, 'destroy'])->name('community_comments.destroy');

    Route::resource('dashboard/users', UserController::class)->only(['index', 'edit', 'update', 'destroy']);




    Route::get('dashboard/certified-researchers', [CertifiedResearcherController::class, 'index'])->name('certified_researchers.index');
    Route::get('dashboard/certified-researchers/{certifiedResearcher}', [CertifiedResearcherController::class, 'show'])->name('certified_researchers.show');
    Route::post('dashboard/certified-researchers/{certifiedResearcher}/approve', [CertifiedResearcherController::class, 'approve'])->name('certified_researchers.approve');
    Route::post('dashboard/certified-researchers/{certifiedResearcher}/reject', [CertifiedResearcherController::class, 'reject'])->name('certified_researchers.reject');
    Route::delete('dashboard/certified-researchers/{certifiedResearcher}', [CertifiedResearcherController::class, 'destroy'])->name('certified_researchers.destroy');


    Route::resource('dashboard/eras', EraController::class)->only(['index', 'create', 'store', 'edit', 'update', 'destroy']);
    Route::resource('dashboard/governorates', GovernorateController::class)->only(['index', 'create', 'store', 'edit', 'update', 'destroy']);

});

require __DIR__ . '/auth.php';
