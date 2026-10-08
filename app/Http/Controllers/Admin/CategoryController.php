<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    public function export()
    {
        return back()->with('success', 'تم تصدير هيكل الأقسام بنجاح');
    }

    public function index()
    {
        // Fetch root categories with detailed statistics
        $categories = Category::whereNull('parent_id')
            ->withCount([
                'products as total_count',
                'products as gold_count' => function ($query) {
                    $query->where('material_type', 'like', '%gold%')
                          ->orWhere('material_type', 'like', '%ذهب%');
                },
                'products as silver_count' => function ($query) {
                    $query->where('material_type', 'like', '%silver%')
                          ->orWhere('material_type', 'like', '%فضة%');
                },
                'products as stone_count' => function ($query) {
                    $query->whereNotNull('stone_type')
                          ->where('stone_type', '!=', '');
                }
            ])
            ->orderBy('display_order')
            ->get();

        return view('categories.index', compact('categories'));
    }

    public function create()
    {
        $parentCategories = Category::whereNull('parent_id')->get();
        return view('categories.create', compact('parentCategories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'parent_id' => 'nullable|exists:categories,id',
            'display_order' => 'nullable|integer',
            'image' => 'nullable|image|max:10240',
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = app(\App\Services\ImageKitService::class)->upload($request->file('image'));
        }

        Category::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name) ?: Str::random(10),
            'parent_id' => $request->parent_id,
            'image' => $imagePath,
            'display_order' => $request->display_order ?? 0,
            'is_active' => true,
        ]);

        return redirect()->route('categories.index')->with('success', 'تم إضافة القسم بنجاح');
    }

    public function edit($id)
    {
        $category = Category::findOrFail($id);
        $parentCategories = Category::whereNull('parent_id')->where('id', '!=', $id)->get();
        return view('categories.edit', compact('category', 'parentCategories'));
    }

    public function update(Request $request, $id)
    {
        $category = Category::findOrFail($id);
        
        $request->validate([
            'name' => 'required|string|max:255',
            'parent_id' => 'nullable|exists:categories,id',
            'display_order' => 'nullable|integer',
            'image' => 'nullable|image|max:10240',
        ]);

        $data = [
            'name' => $request->name,
            'slug' => Str::slug($request->name) ?: $category->slug,
            'parent_id' => $request->parent_id,
            'display_order' => $request->display_order ?? $category->display_order,
        ];

        if ($request->hasFile('image')) {
            // Delete old image if local
            if ($category->image && !str_starts_with($category->image, 'http')) {
                Storage::disk('public')->delete($category->image);
            }
            $data['image'] = app(\App\Services\ImageKitService::class)->upload($request->file('image'));
        }

        $category->update($data);

        return redirect()->route('categories.index')->with('success', 'تم تحديث القسم بنجاح');
    }

    public function clear()
    {
        // Delete all local images first
        $categories = Category::whereNotNull('image')->get();
        foreach ($categories as $category) {
            if (!str_starts_with($category->image, 'http')) {
                Storage::disk('public')->delete($category->image);
            }
        }

        // Set all products category_id to null or a default if they refer to these categories
        \DB::table('products')->update(['category_id' => null]);

        // Truncate or Delete All
        \DB::statement('SET FOREIGN_KEY_CHECKS=0');
        Category::query()->delete();
        \DB::statement('SET FOREIGN_KEY_CHECKS=1');

        return redirect()->route('categories.index')->with('success', 'تم مسح جميع الأقسام بنجاح');
    }

    public function destroy($id)
    {
        $category = Category::findOrFail($id);
        
        // Check if has children (sub-categories)
        if ($category->children()->count() > 0) {
            return redirect()->back()->with('error', __('لا يمكن حذف هذا القسم لاحتوائه على أقسام فرعية'));
        }

        // 🔴 Check if has associated products
        if ($category->products()->count() > 0) {
            return redirect()->back()->with('error', __('لا يمكن حذف هذا القسم لوجود منتجات مرتبطة به. يرجى نقل أو حذف المنتجات أولاً.'));
        }

        if ($category->image && !str_starts_with($category->image, 'http')) {
            Storage::disk('public')->delete($category->image);
        }

        $category->delete();
        return back()->with('success', 'تم حذف القسم بنجاح');
    }
}
