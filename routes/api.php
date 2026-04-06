<?php

use App\Http\Controllers\Api\V1\AuthController;

use App\Http\Controllers\Api\V1\ProductController;
use App\Http\Controllers\Api\V1\BookingController;
use App\Http\Controllers\Api\V1\CategoryController;
use App\Http\Controllers\Api\V1\MerchantController;
use App\Http\Controllers\Api\V1\CustomDesignOrderController;
use App\Http\Controllers\Api\V1\ContactController;
use App\Http\Controllers\Api\V1\ReviewController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::prefix('v1')->middleware('throttle:api')->group(function () {
    // Auth Routes
    Route::middleware('throttle:auth')->group(function() {
        Route::post('/register', [AuthController::class, 'register']);
        Route::post('/login', [AuthController::class, 'login']);
        Route::post('/reset-password', [AuthController::class, 'resetPassword']);
        Route::post('/forgot-password/send-code', [AuthController::class, 'sendResetCode']);
        Route::post('/forgot-password/verify-code', [AuthController::class, 'verifyResetCode']);
        Route::post('/forgot-password/reset', [AuthController::class, 'resetPasswordWithCode']);
    });

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::delete('/user', [AuthController::class, 'destroy']);
        Route::post('/update-profile', [AuthController::class, 'updateProfile']);
        Route::post('/change-password', [AuthController::class, 'changePassword']);
        Route::get('/user', function (Request $request) {
            return $request->user();
        });

        // Product Management
        Route::apiResource('products', ProductController::class)->only(['store', 'update', 'destroy'])->names('api.products');

        // Booking Management
        Route::get('/bookings', [BookingController::class, 'index']);
        Route::get('/bookings/{id}', [BookingController::class, 'show']);
        Route::post('/bookings', [BookingController::class, 'store']);
        Route::patch('/bookings/{id}', [BookingController::class, 'update']);
        
        // Notifications
        Route::get('/notifications', [App\Http\Controllers\Api\V1\NotificationController::class, 'index']);
        Route::post('/notifications/{id}/read', [App\Http\Controllers\Api\V1\NotificationController::class, 'markAsRead']);
        Route::post('/notifications/read-all', [App\Http\Controllers\Api\V1\NotificationController::class, 'markAllAsRead']);

        // Reports
        Route::post('/reports', [App\Http\Controllers\Api\V1\ReportController::class, 'store']);

        // Analytics
        Route::get('/merchant/analytics', [App\Http\Controllers\Api\V1\AnalyticsController::class, 'getMerchantStats']);

        // Favorites
        Route::get('/favorites', [App\Http\Controllers\Api\V1\FavoriteController::class, 'index']);
        Route::post('/favorites/toggle', [App\Http\Controllers\Api\V1\FavoriteController::class, 'toggle']);
        Route::get('/favorites/{productId}/check', [App\Http\Controllers\Api\V1\FavoriteController::class, 'check']);

        // Merchant Booking Actions
        Route::get('/merchant/bookings', [App\Http\Controllers\Api\V1\Merchant\BookingController::class, 'index']);
        Route::post('/merchant/bookings/{id}/confirm', [App\Http\Controllers\Api\V1\Merchant\BookingController::class, 'confirm']);
        Route::post('/merchant/bookings/{id}/reject', [App\Http\Controllers\Api\V1\Merchant\BookingController::class, 'reject']);

        // Merchant KYC Verification
        Route::post('/merchant/verification/upload', [App\Http\Controllers\Api\V1\MerchantVerificationController::class, 'upload']);
        Route::get('/merchant/verification/status', [App\Http\Controllers\Api\V1\MerchantVerificationController::class, 'status']);

        // Custom Design Orders
        Route::get('/custom-designs', [CustomDesignOrderController::class, 'index']);
        Route::post('/custom-designs', [CustomDesignOrderController::class, 'store']);
        
        // Merchant Custom Design Actions
        Route::get('/merchant/custom-designs', [App\Http\Controllers\Api\V1\Merchant\CustomDesignOrderController::class, 'index']);
        Route::patch('/merchant/custom-designs/{id}/status', [App\Http\Controllers\Api\V1\Merchant\CustomDesignOrderController::class, 'updateStatus']);

        // Merchant Store Branding
        Route::post('/merchant/branding', [MerchantController::class, 'updateBranding']);

        // Reviews
        Route::post('/reviews', [ReviewController::class, 'store']);
    });


    // Image serving with CORS
    Route::get('/image/{path}', [App\Http\Controllers\Api\V1\ImageController::class, 'show'])->where('path', '.*');
    
    // Public Product Routes
    Route::get('/products', [ProductController::class, 'index']);
    Route::get('/products/{slug}', [ProductController::class, 'show']);
    Route::get('/categories', [CategoryController::class, 'index']);
    Route::get('/materials', [App\Http\Controllers\Api\V1\MaterialController::class, 'index']);
    Route::get('/merchants', [MerchantController::class, 'index']);
    Route::get('/banners', [App\Http\Controllers\Api\V1\BannerController::class, 'index']);
    Route::get('/gold-prices', [App\Http\Controllers\Api\V1\GoldPriceController::class, 'index']);
    Route::get('/contacts', [ContactController::class, 'index']);
    Route::get('/settings/contact', [ContactController::class, 'index']); // Sync for app compat
    Route::get('/settings/exchange-rates', [App\Http\Controllers\Api\V1\SettingsController::class, 'getExchangeRates']);
    Route::get('/reviews', [ReviewController::class, 'index']);

    
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/categories', [CategoryController::class, 'store']);
        Route::delete('/categories/all', [CategoryController::class, 'deleteAll']);
        Route::delete('/categories/{id}', [CategoryController::class, 'destroy']);
    });
    Route::get('/test-notify', function () {
        event(new \App\Events\GlobalNotificationEvent([
            'title' => 'اختبار إشعارات مجوهراتي',
            'body' => 'إذا رأيت هذا، فالسيرفر العالمي متصل بنجاح 🚀',
            'type' => 'test'
        ]));
        return response()->json(['message' => 'Notification sent!']);
    });
});

