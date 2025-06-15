<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\ArticleComment;
use App\Models\ArticleRating;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ArticleController extends Controller
{
    /**
     * Get all articles with pagination
     */
    public function index()
    {
        return response()->json(
            Article::with([
                'era',
                'governorate',
                'images',
                'comments' => fn($q) => $q->where('is_approved', true)->with('user'),
                'ratings' => fn($q) => $q->where('is_approved', true),
                'user' // Include user who created the article
            ])->paginate(15)
        );
    }

    /**
     * Get a specific article
     */
    public function show($id)
    {
        return response()->json(
            Article::with([
                'era',
                'governorate',
                'images',
                'comments' => fn($q) => $q->where('is_approved', true)->with('user'),
                'ratings' => fn($q) => $q->where('is_approved', true)
            ])->findOrFail($id)
        );
    }

    /**
     * Add a comment to an article
     */
    public function comment(Request $request, $id)
    {
        $request->validate([
            'content' => ['required', 'string', 'min:3']
        ]);

        ArticleComment::create([
            'article_id' => $id,
            'user_id' => Auth::id(),
            'content' => $request->input('content')
        ]);

        return response()->json(['message' => 'Comment submitted for approval'], 201);
    }

    /**
     * Rate an article (Create/Update rating) - Only for researchers and admins
     */
    public function rate(Request $request, $id)
    {
        // Check if user has permission to rate (researcher or admin only)
        $user = Auth::user();
        if (!in_array($user->type, ['researcher', 'admin'])) {
            return response()->json([
                'message' => 'Only researchers and administrators can rate articles'
            ], 403);
        }

        $request->validate([
            'rating' => ['required', 'integer', 'between:1,5']
        ]);

        $article = Article::findOrFail($id);

        // Check if user already rated this article
        $existingRating = ArticleRating::where('article_id', $id)
            ->where('user_id', Auth::id())
            ->first();

        if ($existingRating) {
            // Update existing rating
            $existingRating->update([
                'rating' => $request->rating,
                'is_approved' => false // Reset approval status when updated
            ]);
            $message = 'Rating updated and submitted for approval';
        } else {
            // Create new rating
            ArticleRating::create([
                'article_id' => $id,
                'user_id' => Auth::id(),
                'rating' => $request->rating,
                'is_approved' => false
            ]);
            $message = 'Rating submitted for approval';
        }

        // Get updated article with rating stats
        $article = Article::with([
            'ratings' => fn($q) => $q->where('is_approved', true)
        ])
            ->withCount(['ratings' => fn($q) => $q->where('is_approved', true)])
            ->withAvg(['ratings' => fn($q) => $q->where('is_approved', true)], 'rating')
            ->findOrFail($id);

        return response()->json([
            'message' => $message,
            'rating_stats' => [
                'average_rating' => round($article->ratings_avg_rating ?? 0, 1),
                'total_ratings' => $article->ratings_count,
                'user_rating' => $request->rating
            ]
        ], 201);
    }

    /**
     * Get user's rating for an article - Only for researchers and admins
     */
    public function getUserRating($id)
    {
        // Check if user has permission to rate
        $user = Auth::user();
        if (!in_array($user->type, ['researcher', 'admin'])) {
            return response()->json([
                'message' => 'Only researchers and administrators can rate articles'
            ], 403);
        }

        $userRating = ArticleRating::where('article_id', $id)
            ->where('user_id', Auth::id())
            ->first();

        return response()->json([
            'user_rating' => $userRating ? [
                'rating' => $userRating->rating,
                'is_approved' => $userRating->is_approved,
                'created_at' => $userRating->created_at
            ] : null
        ]);
    }

    /**
     * Delete user's rating - Only for researchers and admins
     */
    public function deleteRating($id)
    {
        // Check if user has permission to rate
        $user = Auth::user();
        if (!in_array($user->type, ['researcher', 'admin'])) {
            return response()->json([
                'message' => 'Only researchers and administrators can rate articles'
            ], 403);
        }

        $rating = ArticleRating::where('article_id', $id)
            ->where('user_id', Auth::id())
            ->first();

        if (!$rating) {
            return response()->json(['message' => 'Rating not found'], 404);
        }

        $rating->delete();

        // Get updated article stats
        $article = Article::withCount(['ratings' => fn($q) => $q->where('is_approved', true)])
            ->withAvg(['ratings' => fn($q) => $q->where('is_approved', true)], 'rating')
            ->findOrFail($id);

        return response()->json([
            'message' => 'Rating deleted successfully',
            'rating_stats' => [
                'average_rating' => round($article->ratings_avg_rating ?? 0, 1),
                'total_ratings' => $article->ratings_count
            ]
        ]);
    }

    /**
     * Get all ratings for an article (with pagination)
     */
    public function getArticleRatings($id, Request $request)
    {
        $perPage = min($request->get('per_page', 10), 50);

        $ratings = ArticleRating::where('article_id', $id)
            ->where('is_approved', true)
            ->with('user:id,name')
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);

        $article = Article::withCount(['ratings' => fn($q) => $q->where('is_approved', true)])
            ->withAvg(['ratings' => fn($q) => $q->where('is_approved', true)], 'rating')
            ->findOrFail($id);

        return response()->json([
            'ratings' => $ratings->items(),
            'pagination' => [
                'current_page' => $ratings->currentPage(),
                'last_page' => $ratings->lastPage(),
                'per_page' => $ratings->perPage(),
                'total' => $ratings->total(),
            ],
            'rating_stats' => [
                'average_rating' => round($article->ratings_avg_rating ?? 0, 1),
                'total_ratings' => $article->ratings_count,
                'rating_distribution' => $this->getRatingDistribution($id)
            ]
        ]);
    }

    /**
     * Get rating distribution (1-5 stars breakdown)
     */
    private function getRatingDistribution($articleId)
    {
        $distribution = ArticleRating::where('article_id', $articleId)
            ->where('is_approved', true)
            ->selectRaw('rating, COUNT(*) as count')
            ->groupBy('rating')
            ->orderBy('rating', 'desc')
            ->pluck('count', 'rating')
            ->toArray();

        // Ensure all ratings 1-5 are represented
        $result = [];
        for ($i = 5; $i >= 1; $i--) {
            $result[$i . '_stars'] = $distribution[$i] ?? 0;
        }

        return $result;
    }

    // ===============================
    // ADMIN RATING MANAGEMENT METHODS
    // ===============================

    /**
     * Get pending ratings for admin approval
     */
    public function getPendingRatings(Request $request)
    {
        $perPage = min($request->get('per_page', 15), 50);

        $pendingRatings = ArticleRating::where('is_approved', false)
            ->with(['user:id,name', 'article:id,name'])
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);

        return response()->json([
            'pending_ratings' => $pendingRatings->items(),
            'pagination' => [
                'current_page' => $pendingRatings->currentPage(),
                'last_page' => $pendingRatings->lastPage(),
                'per_page' => $pendingRatings->perPage(),
                'total' => $pendingRatings->total(),
            ]
        ]);
    }

    /**
     * Approve a rating
     */
    public function approveRating($ratingId)
    {
        $rating = ArticleRating::findOrFail($ratingId);

        $rating->update(['is_approved' => true]);

        return response()->json([
            'message' => 'Rating approved successfully',
            'rating' => $rating->load(['user:id,name', 'article:id,name'])
        ]);
    }

    /**
     * Reject/Delete a rating
     */
    public function rejectRating($ratingId)
    {
        $rating = ArticleRating::findOrFail($ratingId);
        $rating->delete();

        return response()->json([
            'message' => 'Rating rejected and deleted successfully'
        ]);
    }

    /**
     * Bulk approve ratings
     */
    public function bulkApproveRatings(Request $request)
    {
        $request->validate([
            'rating_ids' => ['required', 'array'],
            'rating_ids.*' => ['integer', 'exists:article_ratings,id']
        ]);

        $approvedCount = ArticleRating::whereIn('id', $request->rating_ids)
            ->update(['is_approved' => true]);

        return response()->json([
            'message' => "{$approvedCount} ratings approved successfully",
            'approved_count' => $approvedCount
        ]);
    }

    /**
     * Bulk reject ratings
     */
    public function bulkRejectRatings(Request $request)
    {
        $request->validate([
            'rating_ids' => ['required', 'array'],
            'rating_ids.*' => ['integer', 'exists:article_ratings,id']
        ]);

        $deletedCount = ArticleRating::whereIn('id', $request->rating_ids)->delete();

        return response()->json([
            'message' => "{$deletedCount} ratings rejected and deleted successfully",
            'deleted_count' => $deletedCount
        ]);
    }

    /**
     * Get rating statistics for admin dashboard
     */
    public function getRatingStatistics()
    {
        $stats = [
            'total_ratings' => ArticleRating::count(),
            'approved_ratings' => ArticleRating::where('is_approved', true)->count(),
            'pending_ratings' => ArticleRating::where('is_approved', false)->count(),
            'average_rating' => round(ArticleRating::where('is_approved', true)->avg('rating'), 2),
            'top_rated_articles' => Article::withCount(['ratings' => fn($q) => $q->where('is_approved', true)])
                ->withAvg(['ratings' => fn($q) => $q->where('is_approved', true)], 'rating')
                ->having('ratings_count', '>', 0)
                ->orderBy('ratings_avg_rating', 'desc')
                ->limit(5)
                ->get(['id', 'name', 'ratings_count', 'ratings_avg_rating']),
            'rating_distribution' => ArticleRating::where('is_approved', true)
                ->selectRaw('rating, COUNT(*) as count')
                ->groupBy('rating')
                ->orderBy('rating')
                ->pluck('count', 'rating')
                ->toArray()
        ];

        return response()->json($stats);
    }
}
