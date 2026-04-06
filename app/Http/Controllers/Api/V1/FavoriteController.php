<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Favorite;
use App\Models\Product;

class FavoriteController extends Controller
{
    use \App\Traits\ApiResponse;

    public function index(Request $request)
    {
        $favorites = $request->user()->favorites()
            ->with(['product.merchant.user', 'product.images']) // Eager load for UI and accessors
            ->latest()
            ->paginate(20);

        // Privacy and Image transformation
        $productService = app(\App\Services\ProductService::class);
        $favorites->getCollection()->transform(function ($fav) use ($productService) {
            if ($fav->product) {
                $productService->transformProductImages($fav->product);
                
                if ($fav->product->merchant) {
                    $fav->product->merchant->makeHidden(['contact_number', 'whatsapp_number', 'email']);
                }
            }
            return $fav;
        });

        return $this->success($favorites);
    }

    public function toggle(Request $request)
    {
        $request->validate(['product_id' => 'required|exists:products,id']);
        
        $user = $request->user();
        $productId = $request->product_id;

        $favorite = Favorite::where('user_id', $user->id)
            ->where('product_id', $productId)
            ->first();

        if ($favorite) {
            $favorite->delete();
            return $this->success(['is_favorited' => false], 'تم إزالة المنتج من المفضلة');
        } else {
            Favorite::create([
                'user_id' => $user->id,
                'product_id' => $productId
            ]);
            return $this->success(['is_favorited' => true], 'تم إضافة المنتج للمفضلة');
        }
    }

    public function check(Request $request, $productId)
    {
        $exists = Favorite::where('user_id', $request->user()->id)
            ->where('product_id', $productId)
            ->exists();

        return $this->success(['is_favorited' => $exists]);
    }
}
