<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Services\ProductService;
use App\Services\ImageKitService;
use App\Http\Requests\Api\V1\StoreProductRequest;
use App\Http\Requests\Api\V1\UpdateProductRequest;
use App\Http\Resources\Api\V1\ProductResource;
use Illuminate\Http\Request;
use App\Models\Product;

class ProductController extends Controller
{
    protected $productService;

    public function __construct(ProductService $productService)
    {
        $this->productService = $productService;
    }

    public function index(Request $request)
    {
        $products = $this->productService->getAllProducts($request->all());
        return ProductResource::collection($products);
    }

    public function show($slug)
    {
        try {
            $product = $this->productService->getProductBySlug($slug);
            return new ProductResource($product);
        } catch (\Exception $e) {
            return $this->error('المنتج غير موجود', 404);
        }
    }

    public function store(StoreProductRequest $request)
    {
        $user = $request->user();
        $data = $request->validated();
        $data['merchant_id'] = $user->merchant_id;
        
        $product = $this->productService->createProduct($data);
        return new ProductResource($product);
    }

    public function update(UpdateProductRequest $request, $id)
    {
        $user = $request->user();
        $product = Product::find($id);

        if (!$product) {
            return $this->error('المنتج غير موجود', 404);
        }

        if ($user->role === 'merchant' && $product->merchant_id !== $user->merchant_id) {
            return $this->error('لا يمكنك تعديل منتج لا تملكه', 403);
        }
        
        try {
            $product = $this->productService->updateProduct($product, $request->validated());
            return new ProductResource($product);
        } catch (\Exception $e) {
            return $this->error('خطأ في الخادم: ' . $e->getMessage(), 500);
        }
    }

    public function destroy(Request $request, $id)
    {
        $user = $request->user();
        if ($user->role !== 'merchant') {
            return $this->error('غير مصرح لك بحذف المنتجات', 403);
        }

        $product = Product::find($id);

        if (!$product) {
            return $this->error('المنتج غير موجود', 404);
        }

        if ($user->role === 'merchant' && ($user->merchant_id === null || $product->merchant_id !== $user->merchant_id)) {
            return $this->error('لا يمكنك حذف منتج لا تملكه', 403);
        }

        try {
            $this->productService->deleteProduct($product);
            return $this->success(null, 'تم حذف المنتج بنجاح');
        } catch (\Exception $e) {
            return $this->error('خطأ في الخادم: ' . $e->getMessage(), 500);
        }
    }
}
