<?php
namespace App\Traits;

trait ApiResponseTrait
{
    public function successResponse($data, $message = 'This API is loaded successfully', $status = 200)
    {
        return response()->json([
            'success' => true,
            'status' => $status,
            'message' => $message,
            'data' => $data,
        ], $status);
    }

    public function errorResponse($message, $status, $errors = null)
    {
        $response = [
            'success' => false,
            'status' => $status,
            'message' => $message,
        ];

        if ($errors) {
            $response['errors'] = $errors;
        }

        return response()->json($response, $status);
    }
}
?>
