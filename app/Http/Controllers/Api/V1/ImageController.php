<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ImageController extends Controller
{
    public function show($path)
    {
        // Decode the path
        $decodedPath = base64_decode($path);
        
        $disk = Storage::disk('public');
        $exists = $disk->exists($decodedPath);
        
        if (!$exists) {
            // ✅ الحل الذكي: بدلاً من 404، نعرض صورة افتراضية
            return response()->redirectTo('https://placehold.co/600x400?text=Image+Not+Found');
        }
        
        $fullPath = $disk->path($decodedPath);
        
        // Detect the correct MIME type
        $mimeType = mime_content_type($fullPath) ?: 'image/jpeg';
        
        // Create response with proper headers
        $response = new BinaryFileResponse($fullPath);
        
        // Add CORS headers
        $response->headers->set('Access-Control-Allow-Origin', '*');
        $response->headers->set('Access-Control-Allow-Methods', 'GET, OPTIONS');
        $response->headers->set('Access-Control-Allow-Headers', 'Content-Type, Authorization');
        
        // Set correct content type
        $response->headers->set('Content-Type', $mimeType);
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        
        // Set strong cache headers (1 year)
        $response->headers->set('Cache-Control', 'public, max-age=31536000, immutable');
        $response->headers->set('Vary', 'Accept-Encoding');
        
        return $response;
    }
}
