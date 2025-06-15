<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\ArticleRating;
use App\Models\Article;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ArticleRatingController extends Controller
{
    /**
     * Display ratings with filtering options
     */
    public function index(Request $request)
    {
        $query = ArticleRating::with(['article:id,name', 'user:id,name'])
            ->orderBy('created_at', 'desc');

        // Filter by approval status
        if ($request->filled('status')) {
            if ($request->status === 'pending') {
                $query->where('is_approved', false);
            } elseif ($request->status === 'approved') {
                $query->where('is_approved', true);
            }
        }

        // Filter by rating value
        if ($request->filled('rating')) {
            $query->where('rating', $request->rating);
        }

        // Filter by article
        if ($request->filled('article_id')) {
            $query->where('article_id', $request->article_id);
        }

        // Search by user name or article name
        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->whereHas('user', function ($q) use ($searchTerm) {
                $q->where('name', 'like', "%{$searchTerm}%");
            })->orWhereHas('article', function ($q) use ($searchTerm) {
                $q->where('name', 'like', "%{$searchTerm}%");
            });
        }

        $ratings = $query->paginate(15);

        // Get filter data for dropdowns
        $articles = Article::select('id', 'name')->orderBy('name')->get();
        $pendingCount = ArticleRating::where('is_approved', false)->count();
        $approvedCount = ArticleRating::where('is_approved', true)->count();

        return view('dashboard.ratings.index', compact(
            'ratings',
            'articles',
            'pendingCount',
            'approvedCount'
        ));
    }

    /**
     * Approve a rating
     */
    public function approve($id)
    {
        try {
            $rating = ArticleRating::findOrFail($id);

            if ($rating->is_approved) {
                return back()->with('warning', 'Rating is already approved');
            }

            $rating->update(['is_approved' => true]);

            // Log the approval action
            \Log::info("Rating approved", [
                'rating_id' => $rating->id,
                'article_id' => $rating->article_id,
                'user_id' => $rating->user_id,
                'rating_value' => $rating->rating,
                'approved_by' => auth()->id()
            ]);

            return back()->with('success', 'Rating approved successfully');

        } catch (\Exception $e) {
            \Log::error('Error approving rating: ' . $e->getMessage());
            return back()->with('error', 'Failed to approve rating');
        }
    }

    /**
     * Reject/Delete a rating
     */
    public function destroy($id)
    {
        try {
            $rating = ArticleRating::findOrFail($id);

            // Log the rejection action before deletion
            \Log::info("Rating rejected and deleted", [
                'rating_id' => $rating->id,
                'article_id' => $rating->article_id,
                'user_id' => $rating->user_id,
                'rating_value' => $rating->rating,
                'rejected_by' => auth()->id()
            ]);

            $rating->delete();

            return back()->with('success', 'Rating deleted successfully');

        } catch (\Exception $e) {
            \Log::error('Error deleting rating: ' . $e->getMessage());
            return back()->with('error', 'Failed to delete rating');
        }
    }

    /**
     * Bulk approve ratings
     */
    public function bulkApprove(Request $request)
    {
        $request->validate([
            'rating_ids' => 'required|array',
            'rating_ids.*' => 'exists:article_ratings,id'
        ]);

        try {
            $approvedCount = ArticleRating::whereIn('id', $request->rating_ids)
                ->where('is_approved', false)
                ->update(['is_approved' => true]);

            \Log::info("Bulk ratings approved", [
                'count' => $approvedCount,
                'rating_ids' => $request->rating_ids,
                'approved_by' => auth()->id()
            ]);

            return back()->with('success', "{$approvedCount} ratings approved successfully");

        } catch (\Exception $e) {
            \Log::error('Error bulk approving ratings: ' . $e->getMessage());
            return back()->with('error', 'Failed to approve ratings');
        }
    }

    /**
     * Bulk delete ratings
     */
    public function bulkDelete(Request $request)
    {
        $request->validate([
            'rating_ids' => 'required|array',
            'rating_ids.*' => 'exists:article_ratings,id'
        ]);

        try {
            $deletedCount = ArticleRating::whereIn('id', $request->rating_ids)->count();
            ArticleRating::whereIn('id', $request->rating_ids)->delete();

            \Log::info("Bulk ratings deleted", [
                'count' => $deletedCount,
                'rating_ids' => $request->rating_ids,
                'deleted_by' => auth()->id()
            ]);

            return back()->with('success', "{$deletedCount} ratings deleted successfully");

        } catch (\Exception $e) {
            \Log::error('Error bulk deleting ratings: ' . $e->getMessage());
            return back()->with('error', 'Failed to delete ratings');
        }
    }

    /**
     * Show rating details
     */
    public function show($id)
    {
        $rating = ArticleRating::with([
            'article:id,name,description',
            'user:id,name,email'
        ])->findOrFail($id);

        // Get article rating statistics
        $articleStats = ArticleRating::where('article_id', $rating->article_id)
            ->where('is_approved', true)
            ->selectRaw('
                                        COUNT(*) as total_ratings,
                                        AVG(rating) as average_rating,
                                        SUM(CASE WHEN rating = 5 THEN 1 ELSE 0 END) as five_stars,
                                        SUM(CASE WHEN rating = 4 THEN 1 ELSE 0 END) as four_stars,
                                        SUM(CASE WHEN rating = 3 THEN 1 ELSE 0 END) as three_stars,
                                        SUM(CASE WHEN rating = 2 THEN 1 ELSE 0 END) as two_stars,
                                        SUM(CASE WHEN rating = 1 THEN 1 ELSE 0 END) as one_star
                                    ')
            ->first();

        return view('dashboard.ratings.show', compact('rating', 'articleStats'));
    }

    /**
     * Get rating statistics for dashboard
     */
    public function statistics()
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
                ->get(['id', 'name']),
            'rating_distribution' => ArticleRating::where('is_approved', true)
                ->selectRaw('rating, COUNT(*) as count')
                ->groupBy('rating')
                ->orderBy('rating')
                ->pluck('count', 'rating')
                ->toArray()
        ];

        return view('dashboard.ratings.statistics', compact('stats'));
    }
}
