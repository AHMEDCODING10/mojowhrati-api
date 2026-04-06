<?php

if (!function_exists('image_url')) {
    /**
     * Generate image URL through API route with CORS support
     */
    function image_url($path)
    {
        if (empty($path)) {
            return null;
        }
        
        // Remove 'storage/' prefix if present
        $path = str_replace('storage/', '', $path);
        
        // Encode the path
        $encodedPath = base64_encode($path);
        
        // Return API route URL
        return url("/api/v1/image/{$encodedPath}");
    }
}
