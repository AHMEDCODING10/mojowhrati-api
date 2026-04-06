<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function index(Request $request)
    {
        $query = $request->get('q');
        
        if (empty($query)) {
            return redirect()->route('dashboard');
        }

        $results = [
            'products' => \App\Models\Product::where('title', 'like', "%{$query}%")
                ->orWhere('description', 'like', "%{$query}%")
                ->with(['merchant', 'material'])
                ->limit(5)
                ->get(),
            
            'users' => \App\Models\User::where('name', 'like', "%{$query}%")
                ->orWhere('email', 'like', "%{$query}%")
                ->orWhere('phone', 'like', "%{$query}%")
                ->limit(5)
                ->get(),
            
            'merchants' => \App\Models\Merchant::where('store_name', 'like', "%{$query}%")
                ->orWhereHas('user', function($q) use ($query) {
                    $q->where('name', 'like', "%{$query}%");
                })
                ->with('user')
                ->limit(5)
                ->get(),
            
            'bookings' => \App\Models\Booking::whereHas('customer', function($q) use ($query) {
                    $q->where('name', 'like', "%{$query}%");
                })
                ->orWhereHas('product', function($q) use ($query) {
                    $q->where('title', 'like', "%{$query}%");
                })
                ->with(['customer', 'product', 'merchant'])
                ->limit(5)
                ->get(),
        ];

        $totalResults = $results['products']->count() + 
                       $results['users']->count() + 
                       $results['merchants']->count() + 
                       $results['bookings']->count();

        return view('search.index', compact('results', 'query', 'totalResults'));
    }
}
