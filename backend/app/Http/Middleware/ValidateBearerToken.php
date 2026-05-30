<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ValidateBearerToken
{
    /**
     * Handle an incoming request.
     * 
     * Validates that the request includes a valid Bearer token in the Authorization header.
     * This middleware works in conjunction with 'auth:sanctum' middleware.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @return Response
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Get Authorization header
        $authHeader = $request->header('Authorization');

        // Check if Authorization header exists
        if (!$authHeader) {
            return response()->json([
                'success' => false,
                'message' => 'Missing Authorization header',
                'errors' => [
                    'authorization' => 'Authorization header is required'
                ],
            ], 401);
        }

        // Check if it's a Bearer token
        if (!str_starts_with($authHeader, 'Bearer ')) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid Authorization header format',
                'errors' => [
                    'authorization' => 'Authorization header must use Bearer token format: Bearer <token>'
                ],
            ], 401);
        }

        // Extract token
        $token = substr($authHeader, 7); // Remove "Bearer " prefix

        // Check if token is not empty
        if (empty($token)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid Bearer token',
                'errors' => [
                    'authorization' => 'Bearer token cannot be empty'
                ],
            ], 401);
        }

        // Token will be validated by 'auth:sanctum' middleware
        // This middleware is just for Bearer format validation
        return $next($request);
    }
}
