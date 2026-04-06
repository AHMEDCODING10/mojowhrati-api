<?php

namespace App\Observers;

use App\Models\Product;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ProductObserver
{
    /**
     * Handle the Product "saving" event.
     * We use saving to intercept the images before it gets stored permanently
     * or at least before we finish the request.
     */
    public function created(Product $product): void
    {
        $this->optimizeProductImages($product);
    }

    public function updated(Product $product): void
    {
        // If image fields changed, re-optimize
        if ($product->isDirty('specifications') || $product->wasRecentlyCreated) {
             $this->optimizeProductImages($product);
        }
    }

    /**
     * Optimize all images related to the product
     */
    protected function optimizeProductImages(Product $product)
    {
        // Extract images from specifications if they are stored there, 
        // or from the dedicated images table through relationship
        // For Mojawharati, images are typically in product_images table
        
        if ($product->relationLoaded('images')) {
            foreach ($product->images as $image) {
                $this->optimizeImage($image->image_url);
            }
        }
    }

    protected function optimizeImage($path)
    {
        if (!$path || !Storage::exists($path)) return;

        $fullPath = Storage::path($path);
        $extension = strtolower(pathinfo($fullPath, PATHINFO_EXTENSION));

        // Skip if not an image we can handle
        if (!in_array($extension, ['jpg', 'jpeg', 'png', 'webp'])) return;

        try {
            // Get image info
            $info = getimagesize($fullPath);
            if (!$info) return;

            // Load image based on extension
            switch ($extension) {
                case 'jpg':
                case 'jpeg':
                    $image = imagecreatefromjpeg($fullPath);
                    break;
                case 'png':
                    $image = imagecreatefrompng($fullPath);
                    imagepalettetotruecolor($image);
                    imagealphablending($image, true);
                    imagesavealpha($image, true);
                    break;
                case 'webp':
                    $image = imagecreatefromwebp($fullPath);
                    break;
                default:
                    return;
            }

            if (!$image) return;

            // Resize if too large (max 1200px width/height)
            $width = imagesx($image);
            $height = imagesy($image);
            $maxDim = 1600;

            if ($width > $maxDim || $height > $maxDim) {
                $ratio = $width / $height;
                if ($ratio > 1) {
                    $newWidth = $maxDim;
                    $newHeight = $maxDim / $ratio;
                } else {
                    $newWidth = $maxDim * $ratio;
                    $newHeight = $maxDim;
                }

                $newImage = imagecreatetruecolor($newWidth, $newHeight);
                
                // Preserve transparency for PNG/WebP
                if ($extension == 'png' || $extension == 'webp') {
                    imagealphablending($newImage, false);
                    imagesavealpha($newImage, true);
                }

                imagecopyresampled($newImage, $image, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
                $image = $newImage;
            }

            // Save back with compression
            switch ($extension) {
                case 'jpg':
                case 'jpeg':
                    imagejpeg($image, $fullPath, 88); // 88% quality — sharp details for jewelry
                    break;
                case 'png':
                    imagepng($image, $fullPath, 3); // Compression level 3 (0-9) — less lossy
                    break;
                case 'webp':
                    imagewebp($image, $fullPath, 88);
                    break;
            }

            Log::info("Optimized product image: $path");

        } catch (\Exception $e) {
            Log::error("Failed to optimize image $path: " . $e->getMessage());
        }
    }
}
