<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ReviewController extends Controller
{
    public function index(Request $request)
    {
        $productId = $request->query('product_id');
        
        if (!$productId) {
            return response()->json(['message' => 'Product ID is required'], 400);
        }

        $reviews = Review::where('reviewable_id', $productId)
            ->where('reviewable_type', Product::class)
            ->where('status', 'approved')
            ->with(['user:id,name,profile_image'])
            ->latest()
            ->get();

        $formattedReviews = $reviews->map(function ($review) {
            return [
                'id' => $review->id,
                'user_name' => $review->user->name,
                'user_image' => $review->user->profile_image ? asset('storage/' . $review->user->profile_image) : null,
                'rating' => $review->rating,
                'comment' => $review->comment,
                'created_at' => $review->created_at->toIso8601String(),
            ];
        });

        return response()->json(['data' => $formattedReviews]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'product_id' => 'required|exists:products,id',
            'rating' => 'required|numeric|min:1|max:5',
            'comment' => 'required|string|min:3|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            $review = Review::create([
                'user_id' => $request->user()->id,
                'reviewable_id' => $request->product_id,
                'reviewable_type' => Product::class,
                'rating' => (int) $request->rating,
                'comment' => $request->comment,
                'status' => 'approved', 
            ]);
        } catch (\Exception $e) {
            \Log::error('Review Submission Error: ' . $e->getMessage());
            return response()->json([
                'message' => 'Failed to submit review',
                'error' => $e->getMessage()
            ], 500);
        }

        return response()->json([
            'message' => 'Review submitted successfully',
            'data' => [
                'id' => $review->id,
                'user_name' => $request->user()->name,
                'user_image' => $request->user()->profile_image ? asset('storage/' . $request->user()->profile_image) : null,
                'rating' => $review->rating,
                'comment' => $review->comment,
                'created_at' => $review->created_at->toIso8601String(),
            ]
        ], 201);
    }
}
