<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\UploadedFile;

class ImgbbService
{
    protected $apiKey;

    public function __construct()
    {
        // Use the API key provided by the user, fallback to env
        $this->apiKey = env('IMGBB_API_KEY', 'cd13fd374f6b35985fe5bc679a588c7b');
    }

    /**
     * Upload an image to Imgbb and return the direct URL.
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

            $response = Http::asMultipart()
                ->post('https://api.imgbb.com/1/upload', [
                    'key' => $this->apiKey,
                    'image' => base64_encode($imageData),
                ]);

            if ($response->successful()) {
                $url = $response->json('data.url');
                Log::info("Imgbb Upload Success: $url");
                return $url;
            }

            Log::error('Imgbb upload failed: ' . $response->body());
            return null;
        } catch (\Exception $e) {
            Log::error('Imgbb upload exception: ' . $e->getMessage());
            return null;
        }
    }
}
