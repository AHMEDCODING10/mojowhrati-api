<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;

class ImgbbService
{
    protected $privateKey;

    public function __construct()
    {
        // Use the ImageKit Private Key provided by the user
        $this->privateKey = env('IMAGEKIT_PRIVATE_KEY', 'private_6WPi++CglWrqCuxY4Mc07Nk8HsM=');
    }

    /**
     * Upload an image to ImageKit (replacing ImgBB) and return the direct URL.
     * 
     * @param UploadedFile|string $image
     * @return string|null
     */
    public function upload($image)
    {
        if (!$image) return null;

        try {
            // Get content and encode to base64 for reliable transfer
            $imageData = $image instanceof UploadedFile 
                ? file_get_contents($image->getRealPath()) 
                : (file_exists($image) ? file_get_contents($image) : $image);

            // Generate a random filename for ImageKit
            $fileName = $image instanceof UploadedFile
                ? time() . '_' . Str::random(5) . '.' . $image->getClientOriginalExtension()
                : time() . '_' . Str::random(5) . '.jpg';

            $response = Http::withBasicAuth($this->privateKey, '')
                ->asMultipart()
                ->post('https://upload.imagekit.io/api/v1/files/upload', [
                    'file' => base64_encode($imageData),
                    'fileName' => $fileName,
                ]);

            if ($response->successful()) {
                $url = $response->json('url');
                Log::info("ImageKit Upload Success: $url");
                return $url;
            }

            Log::error('ImageKit upload failed: ' . $response->body());
            return null;
        } catch (\Exception $e) {
            Log::error('ImageKit upload exception: ' . $e->getMessage());
            return null;
        }
    }
}
