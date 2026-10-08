<?php

if (!function_exists('image_url')) {
    /**
     * Generate image URL through API route with CORS support
     * - [x] إنشاء خدمة `ImageKitService` للربط مع API السحابي
     * - [x] تعديل `ImageHelper` لدعم الروابط الخارجية
     * - [/] تحديث `ProductService` لرفع صور المنتجات سحابياً
     */
    function image_url($path)
    {
        if (empty($path)) {
            return null;
        }

        // If the path is already a full URL (Imgbb), return it as is
        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return $path;
        }
        
        // Remove 'storage/' prefix if present
        $path = str_replace('storage/', '', $path);
        
        // Encode the path
        $encodedPath = base64_encode($path);
        
        // Return API route URL
        return url("/api/v1/image/{$encodedPath}");
    }
}
