<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\ArticleComment;
use App\Models\ArticleRating;
use App\Models\CertifiedResearcher;
use App\Models\CommunityPost;
use App\Models\Era;
use App\Models\Governorate;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    /**
     * 🏛️ MAIN HERITAGE DASHBOARD - Perfect for Instructor Demo
     * This is the most impressive report that showcases your entire system
     */
    public function heritageDashboard()
    {
        $dashboard_data = [
            'executive_summary' => $this->getExecutiveSummary(),
            'geographic_coverage' => $this->getGeographicCoverage(),
            'heritage_categories' => $this->getHeritageCategoriesBreakdown(),
            'research_community' => $this->getResearchCommunityOverview(),
            'content_quality' => $this->getContentQualityOverview(),
            'platform_activity' => $this->getPlatformActivityStats(),
            'top_performers' => $this->getTopPerformers()
        ];

        return view('dashboard.reports.heritage-dashboard', compact('dashboard_data'));
    }

    /**
     * 📊 Executive Summary - Key Platform Metrics
     */
    private function getExecutiveSummary()
    {
        $total_articles = Article::count();
        $covered_governorates = Governorate::whereHas('articles')->count();
        $total_governorates = Governorate::count();

        return [
            'total_heritage_sites' => $total_articles,
            'governorates_documented' => $covered_governorates,
            'coverage_percentage' => $total_governorates > 0 ? round(($covered_governorates / $total_governorates) * 100, 1) : 0,
            'historical_periods' => Era::whereHas('articles')->count(),
            'active_researchers' => User::where('type', 'researcher')->count(),
            'community_members' => User::where('type', 'normal')->count(),
            'total_user_interactions' => ArticleComment::count() + ArticleRating::count(),
            'platform_health_score' => $this->calculatePlatformHealthScore()
        ];
    }

    /**
     * 🗺️ Geographic Coverage Analysis
     */
    private function getGeographicCoverage()
    {
        return Governorate::select('id', 'name', 'visit_count')
            ->withCount(['articles'])
            ->with(['articles' => function($query) {
                $query->select('governorate_id', 'category')
                    ->with(['ratings' => function($rq) {
                        $rq->where('is_approved', true);
                    }]);
            }])
            ->orderBy('articles_count', 'desc')
            ->get()
            ->map(function($governorate) {
                $avgRating = $governorate->articles->flatMap->ratings->avg('rating');
                $categories = $governorate->articles->pluck('category')->unique()->values();

                return [
                    'name' => $governorate->name,
                    'heritage_sites' => $governorate->articles_count,
                    'visit_count' => $governorate->visit_count,
                    'avg_rating' => $avgRating ? round($avgRating, 2) : 0,
                    'heritage_types' => $categories->count(),
                    'dominant_category' => $categories->first(),
                    'tourism_potential' => $this->calculateTourismScore($governorate)
                ];
            });
    }

    /**
     * 🏛️ Heritage Categories Breakdown
     */
    private function getHeritageCategoriesBreakdown()
    {
        $categories = Article::select('category')
            ->selectRaw('COUNT(*) as count')
            ->with(['ratings' => function($query) {
                $query->where('is_approved', true);
            }])
            ->groupBy('category')
            ->orderBy('count', 'desc')
            ->get()
            ->map(function($item) {
                // Get average rating for this category
                $avgRating = Article::where('category', $item->category)
                    ->with(['ratings' => function($q) {
                        $q->where('is_approved', true);
                    }])
                    ->get()
                    ->flatMap->ratings
                    ->avg('rating');

                // Get most common governorate for this category
                $topGovernorate = Article::where('category', $item->category)
                    ->select('governorate_id')
                    ->selectRaw('COUNT(*) as count')
                    ->groupBy('governorate_id')
                    ->orderBy('count', 'desc')
                    ->with('governorate:id,name')
                    ->first();

                return [
                    'category' => $item->category,
                    'count' => $item->count,
                    'percentage' => round(($item->count / Article::count()) * 100, 1),
                    'avg_rating' => $avgRating ? round($avgRating, 2) : 0,
                    'top_location' => $topGovernorate ? $topGovernorate->governorate->name : 'N/A'
                ];
            });

        return $categories;
    }

    /**
     * 👥 Research Community Overview
     */
    private function getResearchCommunityOverview()
    {
        $certification_stats = CertifiedResearcher::selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status');

        $top_contributors = User::select('id', 'name', 'type', 'created_at')
            ->withCount(['articles', 'articleComments'])
            ->orderBy('articles_count', 'desc')
            ->take(10)
            ->get();

        $researcher_productivity = User::where('type', 'researcher')
            ->withCount(['articles'])
            ->orderBy('articles_count', 'desc')
            ->take(5)
            ->get();

        return [
            'total_applications' => CertifiedResearcher::count(),
            'pending_applications' => $certification_stats['pending'] ?? 0,
            'approved_researchers' => $certification_stats['accepted'] ?? 0,
            'approval_rate' => $this->calculateApprovalRate(),
            'top_contributors' => $top_contributors,
            'productive_researchers' => $researcher_productivity,
            'community_growth' => $this->getCommunityGrowthTrend()
        ];
    }

    /**
     * ⭐ Content Quality Overview
     */
    private function getContentQualityOverview()
    {
        $total_articles = Article::count();
        $articles_with_images = Article::whereHas('images')->count();
        $articles_with_ratings = Article::whereHas('ratings')->count();
        $articles_with_comments = Article::whereHas('comments')->count();

        $moderation_stats = [
            'comments_pending' => ArticleComment::where('is_approved', false)->count(),
            'comments_approved' => ArticleComment::where('is_approved', true)->count(),
            'ratings_pending' => ArticleRating::where('is_approved', false)->count(),
            'ratings_approved' => ArticleRating::where('is_approved', true)->count()
        ];

        return [
            'total_articles' => $total_articles,
            'image_coverage' => $total_articles > 0 ? round(($articles_with_images / $total_articles) * 100, 1) : 0,
            'rating_coverage' => $total_articles > 0 ? round(($articles_with_ratings / $total_articles) * 100, 1) : 0,
            'comment_coverage' => $total_articles > 0 ? round(($articles_with_comments / $total_articles) * 100, 1) : 0,
            'avg_rating' => ArticleRating::where('is_approved', true)->avg('rating'),
            'moderation_efficiency' => $this->calculateModerationEfficiency(),
            'quality_distribution' => $this->getQualityDistribution(),
            'moderation_stats' => $moderation_stats
        ];
    }

    /**
     * 📈 Platform Activity Statistics
     */
    private function getPlatformActivityStats()
    {
        return [
            'total_users' => User::count(),
            'active_users' => $this->getActiveUsersCount(),
            'community_posts' => CommunityPost::count(),
            'total_interactions' => ArticleComment::count() + ArticleRating::count(),
            'monthly_growth' => $this->getMonthlyGrowth(),
            'engagement_rate' => $this->calculateEngagementRate(),
            'most_active_day' => $this->getMostActiveDay()
        ];
    }

    /**
     * 🏆 Top Performers
     */
    private function getTopPerformers()
    {
        return [
            'most_rated_article' => Article::withCount('ratings')
                ->with('governorate:id,name', 'era:id,name')
                ->orderBy('ratings_count', 'desc')
                ->first(),
            'highest_rated_article' => Article::with(['ratings' => function($q) {
                $q->where('is_approved', true);
            }])
                ->get()
                ->filter(function($article) {
                    return $article->ratings->count() >= 5; // At least 5 ratings
                })
                ->sortByDesc(function($article) {
                    return $article->ratings->avg('rating');
                })
                ->first(),
            'most_visited_governorate' => Governorate::orderBy('visit_count', 'desc')->first(),
            'most_productive_era' => Era::withCount('articles')->orderBy('articles_count', 'desc')->first(),
            'top_researcher' => User::where('type', 'researcher')
                ->withCount('articles')
                ->orderBy('articles_count', 'desc')
                ->first()
        ];
    }

    /**
     * 📊 Detailed Geographic Report
     */
    public function geographicAnalysis()
    {
        $geographic_data = Governorate::select('id', 'name', 'brief', 'description', 'visit_count')
            ->withCount('articles')
            ->with(['articles.ratings' => function($query) {
                $query->where('is_approved', true);
            }])
            ->get()
            ->map(function($governorate) {
                $articles = $governorate->articles;
                $allRatings = $articles->flatMap->ratings;

                return [
                    'governorate' => $governorate->name,
                    'heritage_sites' => $governorate->articles_count,
                    'visit_count' => $governorate->visit_count,
                    'avg_rating' => $allRatings->avg('rating'),
                    'total_ratings' => $allRatings->count(),
                    'category_diversity' => $articles->pluck('category')->unique()->count(),
                    'dominant_categories' => $articles->groupBy('category')->map->count()->sortDesc()->take(3),
                    'tourism_score' => $this->calculateTourismScore($governorate),
                    'brief' => $governorate->brief
                ];
            })
            ->sortByDesc('tourism_score');

        return view('dashboard.reports.geographic-analysis', compact('geographic_data'));
    }

    /**
     * ⏰ Historical Timeline Report
     */
    public function historicalTimeline()
    {
        $timeline_data = Era::withCount('articles')
            ->with(['articles' => function($query) {
                $query->select('era_id', 'category', 'governorate_id')
                    ->with('governorate:id,name');
            }])
            ->orderBy('articles_count', 'desc')
            ->get()
            ->map(function($era) {
                $articles = $era->articles;
                return [
                    'era_name' => $era->name,
                    'total_sites' => $era->articles_count,
                    'categories' => $articles->groupBy('category')->map->count(),
                    'geographic_spread' => $articles->pluck('governorate.name')->unique()->values(),
                    'heritage_density' => $era->articles_count > 0 ? round($era->articles_count / Era::sum('articles_count') * 100, 1) : 0
                ];
            });

        return view('dashboard.reports.historical-timeline', compact('timeline_data'));
    }

    /**
     * Helper Methods
     */
    private function calculatePlatformHealthScore()
    {
        $metrics = [
            'content_completeness' => $this->getContentCompletenessScore(),
            'user_engagement' => $this->getUserEngagementScore(),
            'quality_rating' => $this->getQualityRatingScore(),
            'moderation_efficiency' => $this->calculateModerationEfficiency()
        ];

        return round(array_sum($metrics) / count($metrics), 1);
    }

    private function calculateTourismScore($governorate)
    {
        $articlesCount = $governorate->articles_count ?? 0;
        $visitCount = $governorate->visit_count ?? 0;
        $avgRating = $governorate->articles->flatMap->ratings->avg('rating') ?? 0;

        // Weighted scoring: articles (30%), visits (40%), rating (30%)
        return round(($articlesCount * 0.3) + ($visitCount * 0.0004) + ($avgRating * 20 * 0.3), 2);
    }

    private function calculateApprovalRate()
    {
        $total = CertifiedResearcher::whereIn('status', ['accepted', 'rejected'])->count();
        $approved = CertifiedResearcher::where('status', 'accepted')->count();

        return $total > 0 ? round(($approved / $total) * 100, 1) : 0;
    }

    private function calculateModerationEfficiency()
    {
        $totalContent = ArticleComment::count() + ArticleRating::count();
        $approvedContent = ArticleComment::where('is_approved', true)->count() +
            ArticleRating::where('is_approved', true)->count();

        return $totalContent > 0 ? round(($approvedContent / $totalContent) * 100, 1) : 0;
    }

    private function getActiveUsersCount()
    {
        return User::where(function($query) {
            $query->whereHas('articles')
                ->orWhereHas('articleComments')
                ->orWhereHas('communityPosts');
        })->count();
    }

    private function getCommunityGrowthTrend()
    {
        return User::selectRaw('DATE_FORMAT(created_at, "%Y-%m") as month, COUNT(*) as count')
            ->groupBy('month')
            ->orderBy('month', 'desc')
            ->take(6)
            ->get();
    }

    private function getMonthlyGrowth()
    {
        $thisMonth = User::whereMonth('created_at', now()->month)->count();
        $lastMonth = User::whereMonth('created_at', now()->subMonth()->month)->count();

        return $lastMonth > 0 ? round((($thisMonth - $lastMonth) / $lastMonth) * 100, 1) : 0;
    }

    private function calculateEngagementRate()
    {
        $totalUsers = User::count();
        $activeUsers = $this->getActiveUsersCount();

        return $totalUsers > 0 ? round(($activeUsers / $totalUsers) * 100, 1) : 0;
    }

    private function getContentCompletenessScore()
    {
        $total = Article::count();
        $withImages = Article::whereHas('images')->count();
        return $total > 0 ? round(($withImages / $total) * 100, 1) : 0;
    }

    private function getUserEngagementScore()
    {
        return $this->calculateEngagementRate();
    }

    private function getQualityRatingScore()
    {
        $avgRating = ArticleRating::where('is_approved', true)->avg('rating');
        return $avgRating ? round($avgRating * 20, 1) : 0; // Convert 1-5 scale to 0-100
    }

    private function getQualityDistribution()
    {
        return ArticleRating::selectRaw('rating, COUNT(*) as count')
            ->where('is_approved', true)
            ->groupBy('rating')
            ->orderBy('rating')
            ->get();
    }

    private function getMostActiveDay()
    {
        $dayData = Article::selectRaw('DAYNAME(created_at) as day, COUNT(*) as count')
            ->groupBy('day')
            ->orderBy('count', 'desc')
            ->first();

        return $dayData ? $dayData->day : 'N/A';
    }

    /**
     * 📄 Export Reports
     */
    public function exportHeritageDashboard()
    {
        // Implementation for PDF export
        $data = $this->heritageDashboard();
        // Return PDF using DomPDF or similar
    }
}
