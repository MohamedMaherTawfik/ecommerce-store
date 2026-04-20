<?php

namespace App\Http\Controllers\api\admin;

trait apiResponse
{
    // =========================
    // SUCCESS (200)
    // =========================
    public function success($data = null, $message = 'Success')
    {
        return response()->json([
            'success' => true,
            'status' => 200,
            'message' => $message,
            'data' => $data,
        ], 200);
    }

    // =========================
    // CREATED (201)
    // =========================
    public function created($data = null, $message = 'Created Successfully')
    {
        return response()->json([
            'success' => true,
            'status' => 201,
            'message' => $message,
            'data' => $data,
        ], 201);
    }

    // =========================
    // NOT FOUND (404)
    // =========================
    public function notFound($message = 'Not Found')
    {
        return response()->json([
            'success' => false,
            'status' => 404,
            'message' => $message,
            'data' => null,
        ], 404);
    }

    // =========================
    // UNAUTHORIZED (401)
    // =========================
    public function unauthorized($message = 'Unauthorized')
    {
        return response()->json([
            'success' => false,
            'status' => 401,
            'message' => $message,
            'data' => null,
        ], 401);
    }

    // =========================
    // FORBIDDEN (403)
    // =========================
    public function forbidden($message = 'Forbidden')
    {
        return response()->json([
            'success' => false,
            'status' => 403,
            'message' => $message,
            'data' => null,
        ], 403);
    }

    // =========================
    // SERVER ERROR (500)
    // =========================
    public function serverError($message = 'Server Error', $exception = null)
    {
        return response()->json([
            'success' => false,
            'status' => 500,
            'message' => $message,
            'error' => $exception ? $exception->getMessage() : null,
            'data' => null,
        ], 500);
    }
}
