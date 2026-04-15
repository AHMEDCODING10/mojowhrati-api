<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Support\Str;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use App\Services\ImageKitService;

class ProductService
{
    protected $imageKitService;

    public function __construct(ImageKitService $imageKitService)
    {
        $this->imageKitService = $imageKitService;
    }

    public function getAllProducts(array $filters = [])
    {
        $query = Product::with(['merchant.user', 'category', 'material', 'images', 'primaryImage']);

        // ... filters ...
        // (Existing filter logic remains same, just modifying return transformation)
        
        // Improve: Applying filters logic again to be safe in replacement, but mainly focusing on results
        if (isset($filters['category_id'])) {
            $categoryId = $filters['category_id'];
            $categoryIds = \App\Models\Category::where('id', $categoryId)
                ->orWhere('parent_id', $categoryId)
                ->pluck('id');
            $query->whereIn('category_id', $categoryIds);
        }
        
        if (isset($filters['merchant_id'])) {
            $query->where('merchant_id', $filters['merchant_id']);
        }

        if (isset($filters['material_type']) && $filters['material_type'] !== 'all') {
            $query->where('material_type', $filters['material_type']);
        }

        if (isset($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Advanced Filters
        if (isset($filters['min_weight'])) {
            $query->where('weight', '>=', $filters['min_weight']);
        }
        if (isset($filters['max_weight'])) {
            $query->where('weight', '<=', $filters['max_weight']);
        }

        if (isset($filters['purity'])) {
            $query->where('purity', $filters['purity']);
        }

        // 🟢 Intelligent Stock Filtering & Status Checks
        // Hide products that are out of stock for customers, but ALWAYS show them for merchants viewing their own vault
        $user = \Auth::guard('sanctum')->user();
        $userMerchantId = ($user && $user->role === 'merchant') ? $user->merchant_id : null;
        
        // Robust identification: Are we looking at the authorized merchant's own items?
        $isMerchantOwner = false;
        if ($userMerchantId && isset($filters['merchant_id'])) {
             if ((string)$userMerchantId === (string)$filters['merchant_id']) {
                 $isMerchantOwner = true;
             }
        }

        if (!$isMerchantOwner) {
            // 🔒 Guest/Public View: No longer filtering by stock_quantity > 0 
            // so customers can see 'Currently Unavailable' items.
            
            // Guests only see published items by default
            if (!isset($filters['status']) || $filters['status'] === 'all') {
                $query->where('status', 'published');
            } else {
                $query->where('status', $filters['status']);
            }
        } else {
            // 🔓 Owner View: Full visibility unless a specific status is requested
            if (isset($filters['status']) && $filters['status'] !== 'all') {
                $query->where('status', $filters['status']);
            }
            // If status is not set, or set to 'all', the merchant sees everything (published, draft, out-of-stock)
        }
        // If it IS the merchant owner, we don't add the stock > 0 constraint, 
        // allowing them to see their 0-stock items for management.

        $paginator = $query->latest()->paginate($filters['per_page'] ?? 15);
        
        // Privacy: Hide contact info from public product listings
        // Transform image URLs to use API route with CORS
        $paginator->getCollection()->transform(function ($product) {
            if ($product->merchant) {
                // Ensure contact numbers are available, fallback to user phone if needed
                $product->merchant->whatsapp_number = $product->merchant->whatsapp_number 
                    ?? $product->merchant->user->phone ?? '';
                $product->merchant->contact_number = $product->merchant->contact_number 
                    ?? $product->merchant->user->phone ?? '';
                
                // Only hide sensitive fields, keep contact info
                $product->merchant->makeHidden(['email', 'documents', 'commercial_register', 'tax_number']);
            }
            
            return $product;
        });

        return $paginator;
    }

    public function createProduct(array $data)
    {
        // Generate slug from title
        $data['slug'] = Str::slug($data['title'] ?? 'product-' . time());
        
        // Ensure slug is unique
        $originalSlug = $data['slug'];
        $counter = 1;
        while (Product::where('slug', $data['slug'])->exists()) {
            $data['slug'] = $originalSlug . '-' . $counter;
            $counter++;
        }

        // Map frontend field names to backend
        if (isset($data['name'])) {
            $data['title'] = $data['name'];
            unset($data['name']);
        }
        
        // Set default values
        $data['status'] = $data['status'] ?? 'published';
        $data['type'] = $data['type'] ?? 'jewelry';
        
        // Create product
        $product = Product::create($data);

        // Track stock if enabled
        if (isset($data['manage_stock']) && $data['manage_stock']) {
            $product->stock_quantity = $data['stock_quantity'] ?? 0;
            $product->save();
        }

        // Handle images if present
        if (isset($data['images']) && is_array($data['images'])) {
            foreach ($data['images'] as $index => $image) {
                $path = $this->imageKitService->upload($image);
                if ($path) {
                    $product->images()->create([
                        'image_url' => $path,
                        'is_primary' => $index === 0,
                        'display_order' => $index,
                    ]);
                }
            }
        }

        $product->load(['merchant.user', 'category', 'material', 'images']);
        
        // Notify Admins (Consolidated)
        try {
            app(\App\Services\NotificationService::class)->notifyAdmins(
                'product_added',
                'منتج جديد',
                "قام التاجر {$product->merchant->store_name} بإضافة منتج جديد: {$product->title}",
                [
                    'product_id' => $product->id,
                    'merchant_id' => $product->merchant_id,
                    'merchant_name' => $product->merchant->store_name,
                    'icon' => 'package'
                ]
            );
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error("Failed to notify admins of new product: " . $e->getMessage());
        }

        return $this->transformProductImages($product);
    }

    public function updateProduct(Product $product, array $data)
    {
        // Map frontend field names to backend
        if (isset($data['name'])) {
            $data['title'] = $data['name'];
            unset($data['name']);
        }
        
        // Update slug if title changed
        if (isset($data['title']) && $data['title'] !== $product->title) {
            $data['slug'] = Str::slug($data['title']);
            
            // Ensure slug is unique
            $originalSlug = $data['slug'];
            $counter = 1;
            while (Product::where('slug', $data['slug'])->where('id', '!=', $product->id)->exists()) {
                $data['slug'] = $originalSlug . '-' . $counter;
                $counter++;
            }
        }

        // Auto-publish if stock is increased from 0 and no status is provided
        if (isset($data['stock_quantity']) && $data['stock_quantity'] > 0 && !isset($data['status'])) {
            if ($product->stock_quantity <= 0) {
                $data['status'] = 'published';
            }
        }

        // Update product
        $product->update($data);

        // Handle image deletions first
        if (isset($data['delete_image_ids']) && is_array($data['delete_image_ids']) && count($data['delete_image_ids']) > 0) {
            $imagesToDelete = $product->images()->whereIn('id', $data['delete_image_ids'])->get();
            foreach ($imagesToDelete as $img) {
                if ($img->image_url && Storage::disk('public')->exists($img->image_url)) {
                    Storage::disk('public')->delete($img->image_url);
                }
                $img->delete();
            }
        }

        // If replace_all_images flag is set, delete ALL old images first
        if (!empty($data['replace_images'])) {
            $oldImages = $product->images()->get();
            foreach ($oldImages as $img) {
                if ($img->image_url && Storage::disk('public')->exists($img->image_url)) {
                    Storage::disk('public')->delete($img->image_url);
                }
                $img->delete();
            }
        }

        // Now handle new images if present
        if (isset($data['images']) && is_array($data['images'])) {
            $currentMaxOrder = $product->images()->max('display_order') ?? -1;
            $isFirstImage = ($currentMaxOrder === -1);
            
            foreach ($data['images'] as $index => $image) {
                $path = $this->imageKitService->upload($image);
                if ($path) {
                    $product->images()->create([
                        'image_url' => $path,
                        'is_primary' => $isFirstImage && $index === 0,
                        'display_order' => $currentMaxOrder + $index + 1,
                    ]);
                }
            }
        }

        $product->load(['merchant', 'category', 'material', 'images']);

        // 🟡 Automated Stock Notification Check
        $this->checkStockAndNotify($product);

        return $this->transformProductImages($product);
    }

    public function getProductBySlug(string $slug)
    {
        $product = Product::with(['merchant', 'category', 'material', 'images'])
            ->where('slug', $slug)
            ->firstOrFail();
            
        $product->increment('views_count');
        
        return $this->transformProductImages($product);
    }

    /**
     * Transform product images to use the API route with CORS
     * and hide sensitive merchant info.
     */
    public function transformProductImages($product)
    {
        if ($product->images) {
            foreach ($product->images as $image) {
                if (isset($image->image_url)) {
                    $image->url = \image_url($image->image_url);
                }
            }
        }
        
        return $product;
    }

    public function deductStock(Product $product, int $quantity = 1)
    {
        if ($product->manage_stock && $product->stock_quantity >= $quantity) {
            $product->decrement('stock_quantity', $quantity);
            
            // Check if stock reached zero to potentially update status
            if ($product->stock_quantity <= 0) {
                $product->update(['status' => 'sold']);
                
                // 🟡 Automated Stock Notification Check
                $this->checkStockAndNotify($product);
            }
            
            return true;
        }
        return false;
    }

    public function deleteProduct(Product $product)
    {
        // 1. Manually delete related records from other tables to prevent foreign key errors
        \Illuminate\Support\Facades\DB::table('bookings')->where('product_id', $product->id)->delete();
        \Illuminate\Support\Facades\DB::table('favorites')->where('product_id', $product->id)->delete();
        
        // 2. Delete related reviews if using morphMany
        $product->reviews()->delete();

        // 3. Delete related images from storage and DB
        if ($product->images) {
            foreach ($product->images as $image) {
                if ($image->image_url && Storage::disk('public')->exists($image->image_url)) {
                    Storage::disk('public')->delete($image->image_url);
                }
            }
            $product->images()->delete();
        }
        
        // 4. Finally delete the product
        return $product->delete();
    }

    /**
     * Check if a product has run out of stock and notify the merchant if so.
     */
    protected function checkStockAndNotify(Product $product)
    {
        // Only notify if stock is tracked and has just run out (<= 0)
        if ($product->manage_stock && $product->stock_quantity <= 0) {
            $merchantUser = $product->merchant->user ?? \App\Models\User::where('id', $product->merchant->user_id)->first();
            
            if ($merchantUser) {
                try {
                    app(\App\Services\NotificationService::class)->notifyUser(
                        $merchantUser->id,
                        'out_of_stock',
                        'نفدت الكمية الحالية',
                        "المنتج '{$product->title}' نفدت كميته تماماً. يرجى مراجعة وتحديث الكمية لضمان استمرار المبيعات.",
                        [
                            'product_id' => $product->id,
                            'product_name' => $product->title,
                            'type' => 'out_of_stock',
                            'icon' => 'inventory'
                        ]
                    );
                } catch (\Exception $e) {
                    \Log::error("Failed to send out of stock notification: " . $e->getMessage());
                }
            }
        }
    }
}
