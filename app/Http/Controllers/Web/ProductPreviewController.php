<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductPreviewController extends Controller
{
    /**
     * Display the specified product for public web preview.
     *
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function show($id)
    {
        $product = Product::with(['merchant', 'material', 'category', 'images'])->findOrFail($id);
        
        return view('web.products.preview', compact('product'));
    }
}
