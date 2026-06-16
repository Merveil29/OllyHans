<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Auth\RegisterController;
use App\Http\Controllers\Api\Auth\LoginController;
use App\Http\Controllers\Api\Auth\VerifyOtpController;

use App\Http\Controllers\Api\Auth\PasswordResetController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\PublicProductController;
use App\Http\Controllers\Api\Client\ClientProductController;
use App\Http\Controllers\Api\Admin\AdminProductController;
use App\Http\Controllers\Api\Admin\AdminUserController;
use App\Http\Controllers\Api\Admin\AdminProfileController;
use App\Http\Controllers\Api\Admin\AdminCategoryController;
use App\Http\Controllers\Api\Admin\AdminSubCategoryController;
use App\Http\Controllers\Api\Admin\AdminClientController;
use App\Http\Controllers\Api\Admin\AdminDashboardController;
use App\Http\Controllers\Api\AiAssistantController;
use App\Http\Controllers\Api\NewsletterController;
use App\Http\Controllers\Api\Admin\AdminNotificationController;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Public routes with throttle
Route::prefix('v1')->middleware('throttle:public')->group(function () {
    // Authentication routes with specific throttle (5 requests/min)
    Route::middleware('throttle:auth')->group(function () {
        Route::post('/register', [RegisterController::class, 'register']);
        Route::post('/verify-otp', [VerifyOtpController::class, 'verify']);
        Route::post('/login', [LoginController::class, 'login']); // Login commun client/admin

        Route::post('/forgot-password', [PasswordResetController::class, 'forgotPassword']);
        Route::post('/reset-password', [PasswordResetController::class, 'resetPassword']);
        Route::get('/verify-reset-otp/{email}/{otp}', [PasswordResetController::class, 'verifyResetOtp']);
    });
    
    // Vérification disponibilité email/login
    Route::get('/check-email/{email}', [RegisterController::class, 'checkEmail']);
    Route::get('/check-login/{login}', [RegisterController::class, 'checkLogin']);
    
    Route::get('/activate/{code}', function () {
        return response()->json(['message' => 'Activation endpoint']);
    });
    
    // Public products routes
    Route::get('/products/suggestions', [PublicProductController::class, 'suggestions']);
    Route::get('/products', [PublicProductController::class, 'index']);
    Route::get('/products/{id}', [PublicProductController::class, 'show']);
    Route::post('/products/{id}/view', [PublicProductController::class, 'recordView']);
    
    // Public categories routes
    Route::get('/categories', [AdminCategoryController::class, 'index']);
    Route::get('/categories/{id}', [AdminCategoryController::class, 'show']);
    Route::get('/categories/{id}/products', [AdminCategoryController::class, 'getProducts']);
    
    // Public sub-categories routes
    Route::get('/sub-categories/all', [AdminSubCategoryController::class, 'all']);
    
    


    // IA Assistant - accès public avec rate limit dédié
    Route::middleware('throttle:ai')->prefix('ai')->group(function () {
        Route::post('/chat', [AiAssistantController::class, 'chat']);
        Route::get('/health', [AiAssistantController::class, 'health']);
    });

    // Newsletter - accès public
    Route::prefix('newsletter')->group(function () {
        Route::post('/subscribe', [NewsletterController::class, 'subscribe']);
        Route::get('/unsubscribe/{token}', [NewsletterController::class, 'unsubscribe']);
    });

    // Shop routes - Categories
    Route::get('/shop/categories', [\App\Http\Controllers\Api\CategoryController::class, 'index']);
    Route::get('/shop/categories/{slug}', [\App\Http\Controllers\Api\CategoryController::class, 'show']);

    // Shop routes - Product Lines (Gammes)
    Route::get('/shop/product-lines', [\App\Http\Controllers\Api\ProductLineController::class, 'index']);
    Route::get('/shop/product-lines/{slug}', [\App\Http\Controllers\Api\ProductLineController::class, 'show']);

    // Shop routes - Products
    Route::get('/shop/products/suggestions', [\App\Http\Controllers\Api\ShopProductController::class, 'suggestions']);
    Route::get('/shop/products', [\App\Http\Controllers\Api\ShopProductController::class, 'index']);
    Route::get('/shop/products/{slug}', [\App\Http\Controllers\Api\ShopProductController::class, 'show']);

    // Shop routes - Filters
    Route::get('/shop/filters', [\App\Http\Controllers\Api\ShopFilterController::class, 'filters']);

});

// Protected routes (require authentication)
Route::middleware(['auth:sanctum', 'throttle:client'])->prefix('v1')->group(function () {
    // Auth user routes
    Route::post('/logout', [LoginController::class, 'logout']);
    Route::get('/profile', [ProfileController::class, 'show']);
    Route::put('/profile', [ProfileController::class, 'update']);
    Route::post('/profile/image', [ProfileController::class, 'updateImage']);
    Route::delete('/profile/image', [ProfileController::class, 'deleteImage']);
    Route::put('/profile/password', [ProfileController::class, 'updatePassword']);
    
    // Client products routes
    Route::prefix('client')->group(function () {
        Route::get('/produits', [ClientProductController::class, 'index']);
        Route::get('/produits/{id}', [ClientProductController::class, 'show']);
        Route::post('/produits', [ClientProductController::class, 'store']);
        Route::put('/produits/{id}', [ClientProductController::class, 'update']);
        Route::delete('/produits/{id}', [ClientProductController::class, 'destroy']);

        
    });
});

// Admin routes (require authentication + admin role)
Route::middleware(['auth:sanctum', 'admin', 'throttle:admin'])->prefix('v1/admin')->group(function () {
    // Admin profile routes
    Route::get('/profile', [AdminProfileController::class, 'show']);
    Route::put('/profile', [AdminProfileController::class, 'update']);
    Route::post('/profile/image', [AdminProfileController::class, 'updateImage']);
    Route::delete('/profile/image', [AdminProfileController::class, 'deleteImage']);
    Route::put('/profile/password', [AdminProfileController::class, 'updatePassword']);
    
    // Admin users management (gestion des admins)
    Route::get('/users', [AdminUserController::class, 'index']);
    Route::get('/users/{id}', [AdminUserController::class, 'show']);
    Route::post('/users', [AdminUserController::class, 'create']);
    Route::delete('/users/{id}', [AdminUserController::class, 'delete']);
    
    // Categories management
    Route::get('/categories', [AdminCategoryController::class, 'index']);
    Route::get('/categories/{id}', [AdminCategoryController::class, 'show']);
    Route::post('/categories', [AdminCategoryController::class, 'create']);
    Route::put('/categories/{id}', [AdminCategoryController::class, 'update']);
    Route::delete('/categories/{id}', [AdminCategoryController::class, 'delete']);
    
    // Sub-categories management
    Route::get('/sub-categories/all', [AdminSubCategoryController::class, 'all']);
    Route::get('/categories/{categoryId}/sub-categories', [AdminSubCategoryController::class, 'index']);
    Route::get('/sub-categories/{id}', [AdminSubCategoryController::class, 'show']);
    Route::post('/sub-categories', [AdminSubCategoryController::class, 'create']);
    Route::put('/sub-categories/{id}', [AdminSubCategoryController::class, 'update']);
    Route::delete('/sub-categories/{id}', [AdminSubCategoryController::class, 'delete']);
    
    // Dashboard
    Route::get('/dashboard', [AdminDashboardController::class, 'index']);
    Route::get('/stats', [AdminDashboardController::class, 'stats']);
    
    // Products management (Admin)
    Route::get('/products', [AdminProductController::class, 'index']);
    Route::get('/products/stats', [AdminProductController::class, 'stats']);
    Route::post('/products', [AdminProductController::class, 'store']);
    Route::get('/products/{id}', [AdminProductController::class, 'show']);
    Route::post('/products/{id}', [AdminProductController::class, 'update']);
    Route::put('/products/{id}/approve', [AdminProductController::class, 'approve']);
    Route::put('/products/{id}/reject', [AdminProductController::class, 'reject']);
    Route::delete('/products/{id}', [AdminProductController::class, 'destroy']);
    
    // Clients management
    Route::get('/clients', [AdminClientController::class, 'index']);
    Route::get('/clients/{id}', [AdminClientController::class, 'show']);
    Route::delete('/clients/{id}', [AdminClientController::class, 'delete']);
    Route::put('/clients/{id}/jetons', [AdminClientController::class, 'updateJetons']);



    // Newsletter management (admin)
    Route::get('/newsletter/stats', [NewsletterController::class, 'stats']);
    Route::get('/newsletter/subscribers', [NewsletterController::class, 'subscribers']);

    // Notifications — agrégation depuis les tables existantes (sans nouvelle table)
    Route::get('/notifications/stats', [AdminNotificationController::class, 'stats']);
    Route::get('/notifications', [AdminNotificationController::class, 'index']);

    // Shop management - Categories (table `categories`)
    Route::get('/shop/categories', [\App\Http\Controllers\Api\CategoryController::class, 'index']);
    Route::get('/shop/categories/{id}', [\App\Http\Controllers\Api\CategoryController::class, 'show']);

    // Shop management - Product Lines (Gammes)
    Route::get('/shop/product-lines', [\App\Http\Controllers\Api\Admin\AdminProductLineController::class, 'index']);
    Route::get('/shop/product-lines/{id}', [\App\Http\Controllers\Api\Admin\AdminProductLineController::class, 'show']);
    Route::post('/shop/product-lines', [\App\Http\Controllers\Api\Admin\AdminProductLineController::class, 'store']);
    Route::put('/shop/product-lines/{id}', [\App\Http\Controllers\Api\Admin\AdminProductLineController::class, 'update']);
    Route::delete('/shop/product-lines/{id}', [\App\Http\Controllers\Api\Admin\AdminProductLineController::class, 'destroy']);

    // Shop management - Products
    Route::get('/shop/products', [\App\Http\Controllers\Api\Admin\AdminShopProductController::class, 'index']);
    Route::get('/shop/products/{id}', [\App\Http\Controllers\Api\Admin\AdminShopProductController::class, 'show']);
    Route::post('/shop/products', [\App\Http\Controllers\Api\Admin\AdminShopProductController::class, 'store']);
    Route::put('/shop/products/{id}', [\App\Http\Controllers\Api\Admin\AdminShopProductController::class, 'update']);
    Route::delete('/shop/products/{id}', [\App\Http\Controllers\Api\Admin\AdminShopProductController::class, 'destroy']);

    // Shop management - Variants & Stock
    Route::get('/shop/products/{productId}/variants', [\App\Http\Controllers\Api\Admin\AdminVariantController::class, 'index']);
    Route::get('/shop/variants/{id}', [\App\Http\Controllers\Api\Admin\AdminVariantController::class, 'show']);
    Route::post('/shop/variants', [\App\Http\Controllers\Api\Admin\AdminVariantController::class, 'store']);
    Route::put('/shop/variants/{id}', [\App\Http\Controllers\Api\Admin\AdminVariantController::class, 'update']);
    Route::delete('/shop/variants/{id}', [\App\Http\Controllers\Api\Admin\AdminVariantController::class, 'destroy']);
    Route::put('/shop/variants/{id}/stock', [\App\Http\Controllers\Api\Admin\AdminVariantController::class, 'updateStock']);

    // Shop management - Orders
    Route::get('/shop/orders', [\App\Http\Controllers\Api\Admin\AdminOrderController::class, 'index']);
    Route::get('/shop/orders/{id}', [\App\Http\Controllers\Api\Admin\AdminOrderController::class, 'show']);
    Route::put('/shop/orders/{id}/status', [\App\Http\Controllers\Api\Admin\AdminOrderController::class, 'updateStatus']);
    Route::put('/shop/orders/{id}/payment', [\App\Http\Controllers\Api\Admin\AdminOrderController::class, 'updatePayment']);
});
