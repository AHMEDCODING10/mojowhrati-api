<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Traits\ApiResponse;
use App\Services\ImageKitService;

class CategoryController extends Controller
{
    use ApiResponse;
    protected $ImageKitService;

    public function __construct(ImageKitService $ImageKitService)
    {
        $this->ImageKitService = $ImageKitService;
    }

    public function index()
    {
        $categories = Category::withCount(['products' => function($query) {
                $query->where('status', 'published');
            }])
            ->orderBy('display_order')
            ->orderBy('name')
            ->get();

        return $this->success($categories, 'تم جلب الأقسام بنجاح');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'display_order' => 'nullable|integer',
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $this->ImageKitService->upload($request->file('image'));
        }

        $category = Category::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name, '-') . '-' . rand(100, 999),
            'image' => $imagePath,
            'display_order' => $request->display_order ?? 0,
            'is_active' => true,
        ]);

        return $this->success($category, 'تم إضافة القسم بنجاح', 201);
    }

    public function destroy($id)
    {
        $category = Category::findOrFail($id);
        $category->delete();
        return $this->success(null, 'تم حذف القسم بنجاح');
    }

    public function deleteAll()
    {
        \App\Models\Product::whereNotNull('category_id')->update(['category_id' => null]);
        Category::query()->delete();
        return $this->success(null, 'تم حذف جميع الأقسام بنجاح');
    }
}
