<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function export()
    {
        return back()->with('success', 'سيتم تجهيز ملف إحصائيات المنتجات وتنزيله آلياً');
    }

    public function index(Request $request)
    {
        $query = \App\Models\Product::with(['merchant', 'material', 'category']);

        // Filters
        if ($request->filled('search')) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('merchant_id')) {
            $query->where('merchant_id', $request->merchant_id);
        }

        if ($request->filled('karat')) {
            $query->whereHas('material', function($q) use ($request) {
                $q->where('karat', $request->karat);
            });
        }

        if ($request->filled('min_weight')) {
            $query->where('weight', '>=', $request->min_weight);
        }

        if ($request->filled('max_weight')) {
            $query->where('weight', '<=', $request->max_weight);
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        $products = $query->latest()->paginate(15)->withQueryString();
        $merchants = \App\Models\Merchant::all();
        $categories = \App\Models\Category::all();

        return view('products.index', compact('products', 'merchants', 'categories'));
    }

    public function show($id)
    {
        $product = \App\Models\Product::with(['merchant', 'material', 'category'])->findOrFail($id);
        return view('products.show', compact('product'));
    }

    public function approve($id)
    {
        $product = \App\Models\Product::findOrFail($id);
        $product->update(['status' => 'published']);
        return back()->with('success', 'تم نشر المنتج بنجاح');
    }

    public function reject(Request $request, $id)
    {
        $product = \App\Models\Product::findOrFail($id);
        $product->update([
            'status' => 'rejected',
            'review_notes' => $request->notes
        ]);
        return back()->with('success', 'تم رفض المنتج');
    }

    public function create()
    {
        return view('products.create');
    }

    public function edit($id)
    {
        $product = \App\Models\Product::findOrFail($id);
        $categories = \App\Models\Category::all();
        $merchants = \App\Models\Merchant::all();
        return view('products.edit', compact('product', 'categories', 'merchants'));
    }

    public function update(Request $request, $id)
    {
        $product = \App\Models\Product::findOrFail($id);
        $product->update($request->all());
        return redirect()->route('products.index')->with('success', 'تم تحديث المنتج بنجاح');
    }

    public function destroy($id)
    {
        $product = \App\Models\Product::findOrFail($id);
        $product->delete();
        return redirect()->route('products.index')->with('success', 'تم حذف المنتج بنجاح');
    }
}

