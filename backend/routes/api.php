<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\ProductController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Health check
Route::get('/health', function () {
    return response()->json([
        'success' => true,
        'message' => 'Tomodachi Pet Shop API connected',
        'data' => [
            'app' => config('app.name'),
            'environment' => app()->environment(),
            'time' => now()->toIso8601String(),
        ],
    ]);
});

// ========== AUTHENTICATION ROUTES ==========
// Public authentication endpoints (no auth required)
Route::prefix('auth')->group(function () {
    // REQ-AUTH-01 & REQ-AUTH-02: Login endpoint
    // POST /api/auth/login - validasi email & password, kembalikan token JWT/Sanctum
    Route::post('/login', [AuthController::class, 'login'])->name('auth.login');
    
    // Public registration (creates kasir account)
    Route::post('/register', [AuthController::class, 'register'])->name('auth.register.public');
});

// Protected authentication endpoints (require auth:sanctum)
// REQ-AUTH-03: Setiap request ke endpoint terproteksi harus validasi Bearer Token
Route::middleware('auth:sanctum')->prefix('auth')->group(function () {
    // REQ-AUTH-06: Logout endpoint - revoke token Sanctum
    // POST /api/auth/logout
    Route::post('/logout', [AuthController::class, 'logout'])->name('auth.logout');
    
    // Get current authenticated user
    Route::get('/me', [AuthController::class, 'me'])->name('auth.me');
    
    // Refresh token
    Route::post('/refresh-token', [AuthController::class, 'refreshToken'])->name('auth.refresh-token');
    
    // REQ-AUTH-08: Owner registration endpoint - only owner can register admin/kasir
    // POST /api/auth/register-user (authenticated - owner only)
    Route::post('/register-user', [AuthController::class, 'register'])->middleware('check.role:owner')->name('auth.register.owner');
});

// ========== PROTECTED RESOURCE ROUTES ==========
// All resource endpoints require authentication and Bearer token validation
Route::middleware(['auth:sanctum'])->group(function () {
    Route::apiResource('categories', CategoryController::class);
    Route::apiResource('products', ProductController::class);
});
