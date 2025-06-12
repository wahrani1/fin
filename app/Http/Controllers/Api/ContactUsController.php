<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ContactUs;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class ContactUsController extends Controller
{
    use ApiResponseTrait;

    /**
     * Store a new contact message
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => ['required', 'string', 'min:2', 'max:255'],
                'email' => ['required', 'email', 'max:255'],
                'message' => ['required', 'string', 'min:10', 'max:2000'],
            ]);

            // Create the contact message
            $contact = ContactUs::create($validated);

            return $this->successResponse(
                $contact,
                'Your message has been sent successfully.',
                201
            );

        } catch (ValidationException $e) {
            return $this->errorResponse('Validation failed', 422, $e->errors());
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to send message. Please try again later.', 500);
        }
    }

    /**
     * Get all contact messages (Admin only)
     */
    public function index(Request $request)
    {
        try {
            $perPage = $request->get('per_page', 15);
            $contacts = ContactUs::latest()->paginate($perPage);

            return $this->paginatedResponse($contacts, 'Contact messages retrieved successfully');

        } catch (\Exception $e) {
            return $this->errorResponse('Failed to retrieve messages', 500);
        }
    }

    /**
     * Get a specific contact message (Admin only)
     */
    public function show($id)
    {
        try {
            $contact = ContactUs::findOrFail($id);

            return $this->successResponse($contact, 'Contact message retrieved successfully');

        } catch (\Exception $e) {
            return $this->errorResponse('Contact message not found', 404);
        }
    }

    /**
     * Delete a contact message (Admin only)
     */
    public function destroy($id)
    {
        try {
            $contact = ContactUs::findOrFail($id);
            $contact->delete();

            return $this->successResponse(null, 'Contact message deleted successfully');

        } catch (\Exception $e) {
            return $this->errorResponse('Failed to delete message', 500);
        }
    }
}
