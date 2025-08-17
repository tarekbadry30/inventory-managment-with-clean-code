<?php

use Symfony\Component\HttpFoundation\Response;

if (!function_exists('sendSuccessResponse')) {
    /**
     * Send a JSON response with the given data and status code.
     *
     * @param mixed $data
     * @param int $statusCode
     * @return \Illuminate\Http\JsonResponse
     */
    function sendSuccessResponse($data = [], $message = '', $status = Response::HTTP_OK)
    {
        return response()->json([
            'success' => true,
            'data' => $data,
            'message' => $message,
        ], $status);
    }
}
if (!function_exists('sendErrorResponse')) {
    function sendErrorResponse($message, $errors = [], $status = Response::HTTP_BAD_REQUEST)
    {
        return response()->json([
            'success' => false,
            'message' => $message,
            'errors' => $errors,
        ], $status);
    }
}
