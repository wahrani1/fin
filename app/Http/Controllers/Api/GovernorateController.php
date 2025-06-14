<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Governorate;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GovernorateController extends Controller
{
    use ApiResponseTrait;

    /**
     * Retrieve all governorates.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        try {
            // Load governorates with articles and their images
            $governorates = Governorate::with(['articles.images'])->get();
            return $this->successResponse($governorates, 'Governorates retrieved successfully');
        } catch (\Exception $e) {
            \Log::error('Governorate index error: ' . $e->getMessage());
            return $this->errorResponse('Failed to retrieve governorates', 500);
        }
    }

    /**
     * Retrieve a single governorate by ID and increment visit count.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        try {
            // Load governorate with articles and their images
            $governorate = Governorate::with(['articles.images'])->findOrFail($id);

            // Increment visit count atomically to avoid race conditions
            $governorate->increment('visit_count');

            // Update the model's visit_count attribute manually instead of refresh()
            $governorate->visit_count = $governorate->visit_count + 1;

            return $this->successResponse($governorate, 'Governorate retrieved successfully');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            \Log::warning('Governorate not found: ' . $id);
            return $this->errorResponse('Governorate not found', 404);
        } catch (\Exception $e) {
            \Log::error('Governorate show error: ' . $e->getMessage());
            return $this->errorResponse('Failed to retrieve governorate', 500);
        }
    }
    /**
     * Alternative implementation with more advanced visit tracking
     * Uncomment this if you want to prevent multiple counts from same user
     */
    /*
    public function showWithAdvancedTracking($id, Request $request)
    {
        try {
            $governorate = Governorate::with(['articles.images'])->findOrFail($id);

            // Get user ID if authenticated, otherwise use IP address for tracking
            $userId = auth('sanctum')->id();
            $ipAddress = $request->ip();

            // Create a unique identifier for this view
            $viewIdentifier = $userId ? "user_$userId" : "ip_$ipAddress";

            // Use cache to prevent multiple counts within a time window (e.g., 1 hour)
            $cacheKey = "governorate_view_{$id}_{$viewIdentifier}";

            if (!cache()->has($cacheKey)) {
                // Increment visit count only if not viewed recently
                $governorate->increment('visit_count');

                // Cache this view for 1 hour
                cache()->put($cacheKey, true, now()->addHour());

                \Log::info("Visit count incremented for governorate {$id} by {$viewIdentifier}");
            }

            // Refresh to get updated count
            $governorate->refresh();

            return $this->successResponse($governorate, 'Governorate retrieved successfully');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return $this->errorResponse('Governorate not found', 404);
        } catch (\Exception $e) {
            \Log::error('Governorate show error: ' . $e->getMessage());
            return $this->errorResponse('Failed to retrieve governorate', 500);
        }
    }
    */
}
