<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Product;
use App\Observers\ProductObserver;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use App\Models\Booking;
use App\Models\CustomDesignOrder;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        if (config('app.env') === 'production') {
            \Illuminate\Support\Facades\URL::forceScheme('https');
        }

        if (file_exists(app_path('Helpers/ImageHelper.php'))) {
            require_once app_path('Helpers/ImageHelper.php');
        }

        Product::observe(ProductObserver::class);

        // Core Model Real-Time Synchronization Events
        $modelsToSync = [
            Product::class => 'product',
            Booking::class => 'booking',
            CustomDesignOrder::class => 'custom_design',
        ];

        foreach ($modelsToSync as $modelClass => $modelName) {
            $modelClass::saved(function ($model) use ($modelName) {
                try {
                    broadcast(new \App\Events\AppSyncEvent($modelName, 'updated'));
                } catch (\Exception $e) {}
            });
            
            $modelClass::deleted(function ($model) use ($modelName) {
                try {
                    broadcast(new \App\Events\AppSyncEvent($modelName, 'deleted'));
                } catch (\Exception $e) {}
            });
        }

        // Define API Rate Limiter (General Traffic)
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });

        // Define Auth Rate Limiter (Sensitive Actions)
        RateLimiter::for('auth', function (Request $request) {
            return Limit::perMinute(5)->by($request->ip());
        });
    }
}
