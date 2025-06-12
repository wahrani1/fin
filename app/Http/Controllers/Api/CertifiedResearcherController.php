<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CertifiedResearcher;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class CertifiedResearcherController extends Controller
{
    use ApiResponseTrait;

    /**
     * Handle certified researcher application submission.
     *
     * @param Request $request
     * @return JsonResponse
     */
    // Tested on Postman
    public function apply(Request $request): JsonResponse
    {
        try {
            // Validate the request
            $validated = $request->validate([
                'file' => ['required', 'file', 'mimes:pdf,doc,docx', 'max:2048'], // PDF or Word, max 2MB
                'major' => ['required', 'string', 'max:255'], // Major field
            ]);

            // Get authenticated user
            $user = $request->user();

            // Prevent duplicate applications
            if (CertifiedResearcher::where('user_id', $user->id)->exists()) {
                return $this->errorResponse('You have already submitted an application.', 400);
            }

            // Check if file upload was successful
            if (!$request->hasFile('file') || !$request->file('file')->isValid()) {
                return $this->errorResponse('File upload failed or file is invalid.', 400);
            }

            // Store the uploaded file in private storage
            $filePath = $request->file('file')->store('certified_researcher_files', 'public');

            if (!$filePath) {
                return $this->errorResponse('Failed to store the uploaded file.', 500);
            }

            // Create the certified researcher record
            $application = CertifiedResearcher::create([
                'user_id' => $user->id,
                'file' => $filePath,
                'major' => $validated['major'],
                'status' => 'pending',
            ]);

            // Return success response with status
            return $this->successResponse([
                'application' => $application,
                'user' => $user->makeHidden(['password']),
                'status' => [
                    'status' => 'Pending',
                    'message' => 'Your certification is currently under review',
                ],
            ], 'Certified researcher application submitted successfully', 201);

        } catch (ValidationException $e) {
            return $this->errorResponse('Validation errors occurred', 422, $e->errors());
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to process application: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Get the certification status for the authenticated user.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function status(Request $request)
    {
        try {
            $user = $request->user();
            $application = CertifiedResearcher::with('user')
                ->where('user_id', $user->id)
                ->first();

            if (!$application) {
                return $this->errorResponse('No certification application found', 404);
            }

            $statusResponse = [
                'application_id' => $application->id,
                'status' => ucfirst($application->status),
                'major' => $application->major,
                'submitted_at' => $application->created_at,
                'message' => match ($application->status) {
                    'pending' => 'Your certification is currently under review',
                    'accepted' => 'You are accepted, Congratulations 🎉',
                    'rejected' => $application->rejection_reason ?? 'Your application was rejected',
                    default => 'Unknown status',
                },
            ];

            return $this->successResponse($statusResponse, 'Certification status retrieved successfully');

        } catch (\Exception $e) {
            return $this->errorResponse('Failed to retrieve certification status: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Get all certification applications (Admin only).
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request)
    {
        try {
            $applications = CertifiedResearcher::with('user:id,name,email')
                ->orderBy('created_at', 'desc')
                ->paginate(10);

            return $this->successResponse($applications, 'Applications retrieved successfully');

        } catch (\Exception $e) {
            return $this->errorResponse('Failed to retrieve applications: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Approve a certification application (Admin only).
     *
     * @param Request $request
     * @param int $applicationId
     * @return JsonResponse
     */
    public function approve(Request $request, $applicationId): JsonResponse
    {
        try {
            DB::beginTransaction();

            $application = CertifiedResearcher::with('user')->findOrFail($applicationId);

            if ($application->status === 'accepted') {
                return $this->errorResponse('Application is already approved', 400);
            }

            // Update application status
            $application->update([
                'status' => 'accepted',
                'rejection_reason' => null,
            ]);

            // Update user type to researcher
            $application->user->update(['type' => 'researcher']);

            DB::commit();

            return $this->successResponse([
                'application' => $application->fresh(),
                'status' => 'Accepted',
                'message' => 'Application approved successfully',
            ], 'Application approved successfully');

        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorResponse('Failed to approve application: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Reject a certification application (Admin only).
     *
     * @param Request $request
     * @param int $applicationId
     * @return JsonResponse
     */
    public function reject(Request $request, int $applicationId): JsonResponse
    {
        try {
            $validated = $request->validate([
                'rejection_reason' => ['required', 'string', 'max:1000'],
            ]);

            $application = CertifiedResearcher::findOrFail($applicationId);

            if ($application->status === 'rejected') {
                return $this->errorResponse('Application is already rejected', 400);
            }

            $application->update([
                'status' => 'rejected',
                'rejection_reason' => $validated['rejection_reason'],
            ]);

            return $this->successResponse([
                'application' => $application->fresh(),
                'status' => 'Rejected',
                'message' => $validated['rejection_reason'],
            ], 'Application rejected successfully');

        } catch (ValidationException $e) {
            return $this->errorResponse('Validation errors occurred', 422, $e->errors());
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to reject application: ' . $e->getMessage(), 500);
        }
    }
}
