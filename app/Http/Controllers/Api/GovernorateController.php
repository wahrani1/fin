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
            $governorates = Governorate::with('articles')->get();
            return $this->successResponse($governorates, 'Governorates retrieved successfully');
        } catch (\Exception $e) {
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
            $governorate = Governorate::with('articles')->findOrFail($id);
            return $this->successResponse($governorate, 'Governorate retrieved successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Governorate not found', 404);
        }
    }
}
?>
