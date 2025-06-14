<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ArticleResource;
use App\Http\Resources\GovernorateResource;
use App\Models\Article;
use App\Models\Governorate;
use App\Models\Era;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    use ApiResponseTrait;

    /**
     * Search articles with multiple filters
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function searchArticles(Request $request)
    {
        try {
            $query = Article::query();

            // Load relationships for performance
            $query->with([
                'images',
                'era',
                'governorate',
                'user',
                'comments' => function($q) {
                    $q->where('is_approved', true)->with('user')->latest();
                },
                'ratings' => function($q) {
                    $q->where('is_approved', true)->with('user');
                }
            ])
                ->withCount(['comments', 'ratings'])
                ->withAvg('ratings', 'rating');

            // 🔍 TEXT SEARCH
            if ($request->filled('search')) {
                $searchTerm = $request->search;
                $query->where(function($q) use ($searchTerm) {
                    $q->where('name', 'LIKE', "%{$searchTerm}%")
                        ->orWhere('description', 'LIKE', "%{$searchTerm}%");
                });
            }

            // 🏛️ CATEGORY FILTER
            if ($request->filled('category')) {
                $categories = is_array($request->category) ? $request->category : [$request->category];
                $query->whereIn('category', $categories);
            }

            // 🗺️ GOVERNORATE FILTER
            if ($request->filled('governorate_id')) {
                $governorateIds = is_array($request->governorate_id) ? $request->governorate_id : [$request->governorate_id];
                $query->whereIn('governorate_id', $governorateIds);
            }

            // ⏰ ERA FILTER
            if ($request->filled('era_id')) {
                $eraIds = is_array($request->era_id) ? $request->era_id : [$request->era_id];
                $query->whereIn('era_id', $eraIds);
            }

            // 👤 USER FILTER (for user's own articles)
            if ($request->filled('user_id')) {
                $query->where('user_id', $request->user_id);
            }

            // ⭐ RATING FILTER
            if ($request->filled('min_rating')) {
                $query->whereHas('ratings', function($q) use ($request) {
                    $q->where('is_approved', true)
                        ->havingRaw('AVG(rating) >= ?', [$request->min_rating]);
                });
            }

            // 📸 HAS IMAGES FILTER
            if ($request->filled('has_images')) {
                if ($request->boolean('has_images')) {
                    $query->whereHas('images');
                } else {
                    $query->whereDoesntHave('images');
                }
            }

            // 💬 HAS COMMENTS FILTER
            if ($request->filled('has_comments')) {
                if ($request->boolean('has_comments')) {
                    $query->whereHas('comments', function($q) {
                        $q->where('is_approved', true);
                    });
                } else {
                    $query->whereDoesntHave('comments');
                }
            }

            // 📅 DATE RANGE FILTER
            if ($request->filled('date_from')) {
                $query->whereDate('created_at', '>=', $request->date_from);
            }
            if ($request->filled('date_to')) {
                $query->whereDate('created_at', '<=', $request->date_to);
            }

            // 📊 SORTING
            $sortBy = $request->get('sort_by', 'created_at');
            $sortOrder = $request->get('sort_order', 'desc');

            switch ($sortBy) {
                case 'name':
                    $query->orderBy('name', $sortOrder);
                    break;
                case 'rating':
                    $query->orderBy('ratings_avg_rating', $sortOrder);
                    break;
                case 'comments':
                    $query->orderBy('comments_count', $sortOrder);
                    break;
                case 'popularity':
                    $query->orderBy('ratings_count', 'desc')
                        ->orderBy('comments_count', 'desc');
                    break;
                case 'random':
                    $query->inRandomOrder();
                    break;
                default:
                    $query->orderBy('created_at', $sortOrder);
            }

            // 📄 PAGINATION
            $perPage = min($request->get('per_page', 15), 50); // Max 50 items per page
            $articles = $query->paginate($perPage);

            return $this->successResponse([
                'articles' => ArticleResource::collection($articles->items()),
                'pagination' => [
                    'current_page' => $articles->currentPage(),
                    'last_page' => $articles->lastPage(),
                    'per_page' => $articles->perPage(),
                    'total' => $articles->total(),
                    'from' => $articles->firstItem(),
                    'to' => $articles->lastItem(),
                ],
                'filters_applied' => $this->getAppliedFilters($request),
                'total_results' => $articles->total()
            ], 'Search completed successfully');

        } catch (\Exception $e) {
            \Log::error('Search articles error: ' . $e->getMessage());
            return $this->errorResponse('Search failed', 500);
        }
    }

    /**
     * Search governorates
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function searchGovernorates(Request $request)
    {
        try {
            $query = Governorate::query();

            // Load articles count
            $query->withCount('articles');

            //  TEXT SEARCH
            if ($request->filled('search')) {
                $searchTerm = $request->search;
                $query->where(function($q) use ($searchTerm) {
                    $q->where('name', 'LIKE', "%{$searchTerm}%")
                        ->orWhere('brief', 'LIKE', "%{$searchTerm}%")
                        ->orWhere('description', 'LIKE', "%{$searchTerm}%");
                });
            }

            //  FILTER BY ARTICLE COUNT
            if ($request->filled('min_articles')) {
                $query->having('articles_count', '>=', $request->min_articles);
            }

            if ($request->filled('has_articles')) {
                if ($request->boolean('has_articles')) {
                    $query->has('articles');
                } else {
                    $query->doesntHave('articles');
                }
            }

            //  FILTER BY VISIT COUNT
            if ($request->filled('min_visits')) {
                $query->where('visit_count', '>=', $request->min_visits);
            }

            //  SORTING
            $sortBy = $request->get('sort_by', 'name');
            $sortOrder = $request->get('sort_order', 'asc');

            switch ($sortBy) {
                case 'articles_count':
                    $query->orderBy('articles_count', $sortOrder);
                    break;
                case 'visit_count':
                    $query->orderBy('visit_count', $sortOrder);
                    break;
                case 'popularity':
                    $query->orderBy('visit_count', 'desc')
                        ->orderBy('articles_count', 'desc');
                    break;
                default:
                    $query->orderBy('name', $sortOrder);
            }

            $governorates = $query->get();

            return $this->successResponse([
                'governorates' => GovernorateResource::collection($governorates),
                'total_results' => $governorates->count(),
                'filters_applied' => $this->getAppliedFilters($request)
            ], 'Governorates search completed successfully');

        } catch (\Exception $e) {
            \Log::error('Search governorates error: ' . $e->getMessage());
            return $this->errorResponse('Search failed', 500);
        }
    }

    /**
     * Get filter options for frontend
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getFilterOptions()
    {
        try {
            $categories = Article::CATEGORIES;
            $governorates = Governorate::select('id', 'name')->orderBy('name')->get();
            $eras = Era::select('id', 'name')->orderBy('name')->get();

            return $this->successResponse([
                'categories' => $categories,
                'governorates' => $governorates,
                'eras' => $eras,
                'sort_options' => [
                    'created_at' => 'Newest First',
                    'name' => 'Name A-Z',
                    'rating' => 'Highest Rated',
                    'comments' => 'Most Discussed',
                    'popularity' => 'Most Popular',
                    'random' => 'Random'
                ],
                'rating_options' => [
                    1 => '1 Star & Above',
                    2 => '2 Stars & Above',
                    3 => '3 Stars & Above',
                    4 => '4 Stars & Above',
                    5 => '5 Stars Only'
                ]
            ], 'Filter options retrieved successfully');

        } catch (\Exception $e) {
            \Log::error('Get filter options error: ' . $e->getMessage());
            return $this->errorResponse('Failed to get filter options', 500);
        }
    }

    /**
     * Advanced search with multiple entity types
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function globalSearch(Request $request)
    {
        $request->validate([
            'search' => 'required|string|min:2',
            'type' => 'sometimes|in:all,articles,governorates',
            'limit' => 'sometimes|integer|min:1|max:20'
        ]);

        try {
            $searchTerm = $request->search;
            $type = $request->get('type', 'all');
            $limit = $request->get('limit', 10);
            $results = [];

            if ($type === 'all' || $type === 'articles') {
                $articles = Article::where(function($q) use ($searchTerm) {
                    $q->where('name', 'LIKE', "%{$searchTerm}%")
                        ->orWhere('description', 'LIKE', "%{$searchTerm}%");
                })
                    ->with(['images', 'era', 'governorate'])
                    ->withCount(['comments', 'ratings'])
                    ->withAvg('ratings', 'rating')
                    ->limit($limit)
                    ->get();

                $results['articles'] = [
                    'data' => ArticleResource::collection($articles),
                    'count' => $articles->count()
                ];
            }

            if ($type === 'all' || $type === 'governorates') {
                $governorates = Governorate::where(function($q) use ($searchTerm) {
                    $q->where('name', 'LIKE', "%{$searchTerm}%")
                        ->orWhere('brief', 'LIKE', "%{$searchTerm}%");
                })
                    ->withCount('articles')
                    ->limit($limit)
                    ->get();

                $results['governorates'] = [
                    'data' => GovernorateResource::collection($governorates),
                    'count' => $governorates->count()
                ];
            }

            return $this->successResponse([
                'results' => $results,
                'search_term' => $searchTerm,
                'total_results' => collect($results)->sum('count')
            ], 'Global search completed successfully');

        } catch (\Exception $e) {
            \Log::error('Global search error: ' . $e->getMessage());
            return $this->errorResponse('Search failed', 500);
        }
    }

    /**
     * Get trending/popular articles
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getTrending(Request $request)
    {
        try {
            $timeframe = $request->get('timeframe', 'week'); // week, month, all
            $limit = min($request->get('limit', 10), 20);

            $query = Article::with([
                'images',
                'era',
                'governorate',
                'ratings' => function($q) {
                    $q->where('is_approved', true);
                }
            ])
                ->withCount(['comments', 'ratings'])
                ->withAvg('ratings', 'rating');

            // Apply timeframe filter
            if ($timeframe === 'week') {
                $query->where('created_at', '>=', now()->subWeek());
            } elseif ($timeframe === 'month') {
                $query->where('created_at', '>=', now()->subMonth());
            }

            // Sort by popularity (ratings and comments)
            $articles = $query->orderByRaw('(ratings_count + comments_count) DESC')
                ->orderBy('ratings_avg_rating', 'DESC')
                ->limit($limit)
                ->get();

            return $this->successResponse([
                'trending_articles' => ArticleResource::collection($articles),
                'timeframe' => $timeframe,
                'count' => $articles->count()
            ], 'Trending articles retrieved successfully');

        } catch (\Exception $e) {
            \Log::error('Get trending error: ' . $e->getMessage());
            return $this->errorResponse('Failed to get trending articles', 500);
        }
    }

    /**
     * Get applied filters summary
     *
     * @param Request $request
     * @return array
     */
    private function getAppliedFilters(Request $request)
    {
        $filters = [];

        if ($request->filled('search')) {
            $filters['search'] = $request->search;
        }
        if ($request->filled('category')) {
            $filters['category'] = $request->category;
        }
        if ($request->filled('governorate_id')) {
            $filters['governorate_id'] = $request->governorate_id;
        }
        if ($request->filled('era_id')) {
            $filters['era_id'] = $request->era_id;
        }
        if ($request->filled('min_rating')) {
            $filters['min_rating'] = $request->min_rating;
        }
        if ($request->filled('has_images')) {
            $filters['has_images'] = $request->boolean('has_images');
        }
        if ($request->filled('sort_by')) {
            $filters['sort_by'] = $request->sort_by;
        }

        return $filters;
    }
}
?>
