<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Governorate;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;

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
     * Retrieve a single governorate by ID.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        try {
            // Load governorate with articles and their images
            $governorate = Governorate::with(['articles.images'])->findOrFail($id);
            return $this->successResponse($governorate, 'Governorate retrieved successfully');
        } catch (\Exception $e) {
            \Log::error('Governorate show error: ' . $e->getMessage());
            return $this->errorResponse('Governorate not found', 404);
        }
    }
}
?>
