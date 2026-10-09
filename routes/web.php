<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\MerchantController;
use App\Http\Controllers\Admin\BookingController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\NotificationController;
use App\Http\Controllers\Admin\GoldPricesController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\BannerController;

Route::get('/', function () {
    return redirect('/dashboard');
});

// Temporary Route to test deletion end-to-end
Route::get('/debug-logs', function() {
    try {
        $user = \App\Models\User::create([
            'name' => 'Test Delete',
            'email' => 'testdelete_' . time() . '@example.com',
            'phone' => '123456789' . rand(10,99),
            'password' => bcrypt('password'),
            'role' => 'customer',
            'status' => 'active'
        ]);
        
        $output = ['step1_create' => 'success', 'user_id' => $user->id];
        
        $res = $user->delete();
        $output['step2_delete_res'] = $res;
        
        $exists = \App\Models\User::find($user->id);
        $output['step3_still_exists'] = $exists !== null;
        
        return response()->json($output);
    } catch (\Exception $e) {
        return response()->json(['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
    }
});

// Public Product Preview for QR Scans
Route::get('/p/{id}', [\App\Http\Controllers\Web\ProductPreviewController::class, 'show'])->name('products.preview');

// Public App Download Link (For Local APK)
Route::get('/download-app', function() {
    // In a real local setup, you'd place the APK in public/builds/app.apk
    // For now, we'll just redirect to a placeholder or back if not found.
    $path = public_path('builds/mojohrti.apk');
    if (file_exists($path)) {
        return response()->download($path);
    }
    return back()->with('error', 'رابط التحميل غير متوفر حالياً، يرجى مراجعة المسؤول.');
});


Route::middleware(['auth', 'verified', 'role:super_admin,admin,moderator,support', 'permission'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/search', [\App\Http\Controllers\Admin\SearchController::class, 'index'])->name('search');
    
    // Products
    Route::get('/products', [ProductController::class, 'index'])->name('products.index');
    Route::get('/products/create', [ProductController::class, 'create'])->name('products.create');
    Route::get('/products/export', [ProductController::class, 'export'])->name('products.export');
    Route::get('/products/{id}', [ProductController::class, 'show'])->name('products.show');
    Route::post('/products/{id}/approve', [ProductController::class, 'approve'])->name('products.approve');
    Route::post('/products/{id}/reject', [ProductController::class, 'reject'])->name('products.reject');
    Route::get('/products/{id}/edit', [ProductController::class, 'edit'])->name('products.edit');
    Route::post('/products/{id}/update', [ProductController::class, 'update'])->name('products.update');
    Route::delete('/products/{id}', [ProductController::class, 'destroy'])->name('products.destroy');
    // Users Management
    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::get('/users/create', [UserController::class, 'create'])->name('users.create');
    Route::get('/users/export', [UserController::class, 'export'])->name('users.export');
    Route::get('/users/{id}', [UserController::class, 'show'])->name('users.show');
    Route::get('/users/{id}/edit', [UserController::class, 'edit'])->name('users.edit');
    Route::put('/users/{id}/update', [UserController::class, 'update'])->name('users.update');
    Route::post('/users/{id}/toggle', [UserController::class, 'toggleStatus'])->name('users.toggle');
    Route::post('/users/{id}/toggle-status', [UserController::class, 'toggleStatus'])->name('users.toggle-status');
    Route::post('/users', [UserController::class, 'store'])->name('users.store');
    Route::delete('/users/{id}', [UserController::class, 'destroy'])->name('users.destroy');
    
    // Currency Management
    Route::get('/currencies', [\App\Http\Controllers\Admin\CurrencyController::class, 'index'])->name('currencies.index');
    Route::post('/currencies/update', [\App\Http\Controllers\Admin\CurrencyController::class, 'update'])->name('currencies.update');
    
    // Bookings
    Route::get('/bookings', [BookingController::class, 'index'])->name('bookings.index');
    Route::get('/bookings/create', [BookingController::class, 'create'])->name('bookings.create');
    Route::get('/bookings/export', [BookingController::class, 'export'])->name('bookings.export');
    Route::get('/bookings/{id}', [BookingController::class, 'show'])->name('bookings.show');
    Route::post('/bookings/{id}/confirm', [BookingController::class, 'confirm'])->name('bookings.confirm');
    Route::post('/bookings/{id}/reject', [BookingController::class, 'reject'])->name('bookings.reject');
    Route::post('/bookings/{id}/complete', [BookingController::class, 'complete'])->name('bookings.complete');
    Route::patch('/bookings/{id}/status', [BookingController::class, 'updateStatus'])->name('bookings.updateStatus');
    Route::get('/bookings/{id}/edit', [BookingController::class, 'edit'])->name('bookings.edit');
    Route::delete('/bookings/{id}', [BookingController::class, 'destroy'])->name('bookings.destroy');
    
    // Settings
    Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');
    Route::put('/settings', [SettingController::class, 'update'])->name('settings.update');
    Route::get('/settings/lang/{locale}', [SettingController::class, 'changeLocale'])->name('settings.lang');
    Route::post('/settings/theme', [SettingController::class, 'toggleTheme'])->name('settings.theme');
    
    // Reports
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/generate', [ReportController::class, 'generate'])->name('reports.generate');
    Route::get('/reports/export/{type}', [ReportController::class, 'export'])->name('reports.export');

    // Notifications
    Route::post('notifications/broadcast', [NotificationController::class, 'broadcast'])->name('notifications.broadcast');
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/{id}/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');
    Route::post('/notifications/read-all', [NotificationController::class, 'readAll'])->name('notifications.readAll');
    Route::delete('/notifications/{id}', [NotificationController::class, 'destroy'])->name('notifications.destroy');

    // Gold Prices
    Route::post('gold-prices/sync-global', [GoldPricesController::class, 'syncGlobal'])->name('gold-prices.sync');
    Route::post('gold-prices/update-global', [GoldPricesController::class, 'updateGlobalSettings'])->name('gold-prices.update-global');
    Route::post('gold-prices/manual-update', [GoldPricesController::class, 'update'])->name('gold-prices.manual-update');
    Route::resource('gold-prices', GoldPricesController::class);

    // Banners
    Route::post('banners/{banner}/toggle', [BannerController::class, 'toggleStatus'])->name('banners.toggle');
    Route::resource('banners', BannerController::class);

    // Categories
    Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
    Route::get('/categories/create', [CategoryController::class, 'create'])->name('categories.create');
    Route::get('/categories/export', [CategoryController::class, 'export'])->name('categories.export');
    Route::post('/categories', [CategoryController::class, 'store'])->name('categories.store');
    Route::get('/categories/{id}/edit', [CategoryController::class, 'edit'])->name('categories.edit');
    Route::put('/categories/{id}/update', [CategoryController::class, 'update'])->name('categories.update');
    Route::delete('/categories/{id}', [CategoryController::class, 'destroy'])->name('categories.destroy');
    Route::post('/categories/clear', [CategoryController::class, 'clear'])->name('categories.clear');

    // Merchants
    Route::get('/merchants', [MerchantController::class, 'index'])->name('merchants.index');
    Route::get('/merchants/create', [MerchantController::class, 'create'])->name('merchants.create');
    Route::get('/merchants/verify', [MerchantController::class, 'verify'])->name('merchants.verify');
    Route::get('/merchants/export', [MerchantController::class, 'export'])->name('merchants.export');
    Route::get('/merchants/{id}', [MerchantController::class, 'show'])->name('merchants.show');
    Route::post('/merchants/{id}/approve', [MerchantController::class, 'approve'])->name('merchants.approve');
    Route::post('/merchants/{id}/reject', [MerchantController::class, 'reject'])->name('merchants.reject');
    Route::post('/merchants/{id}/unapprove', [MerchantController::class, 'unapprove'])->name('merchants.unapprove');
    Route::delete('/merchants/{id}', [MerchantController::class, 'destroy'])->name('merchants.destroy');

    // Custom Design Orders
    Route::get('/custom-designs', [App\Http\Controllers\Admin\CustomDesignOrderController::class, 'index'])->name('custom_designs.index');
    Route::get('/custom-designs/{id}', [App\Http\Controllers\Admin\CustomDesignOrderController::class, 'show'])->name('custom_designs.show');
    Route::post('/custom-designs/{id}/status', [App\Http\Controllers\Admin\CustomDesignOrderController::class, 'updateStatus'])->name('custom_designs.updateStatus');
    Route::delete('/custom-designs/{id}', [App\Http\Controllers\Admin\CustomDesignOrderController::class, 'destroy'])->name('custom_designs.destroy');

    // Contact Management
    Route::get('/contacts', [App\Http\Controllers\Admin\ContactController::class, 'index'])->name('contacts.index');
    Route::post('/contacts', [App\Http\Controllers\Admin\ContactController::class, 'store'])->name('contacts.store');
    Route::post('/contacts/{id}', [App\Http\Controllers\Admin\ContactController::class, 'update'])->name('contacts.update');
    Route::post('/contacts/{id}/toggle', [App\Http\Controllers\Admin\ContactController::class, 'toggleStatus'])->name('contacts.toggle');
    Route::delete('/contacts/{id}', [App\Http\Controllers\Admin\ContactController::class, 'destroy'])->name('contacts.destroy');
});





Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';

// Storage Fallback for Windows/Permission issues (Fixed 403 Error)
Route::get('/storage/profile_images/{filename}', function ($filename) {
    if (!auth()->check()) abort(401);
    $path = storage_path('app/public/profile_images/' . $filename);
    if (!file_exists($path)) abort(404);
    $file = file_get_contents($path);
    $type = mime_content_type($path);
    return response($file)->header('Content-Type', $type);
});
