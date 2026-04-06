<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\CustomDesignOrder;
use App\Models\Merchant;
use Illuminate\Support\Facades\Validator;
use App\Events\NewNotificationEvent;

class CustomDesignOrderController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $orders = CustomDesignOrder::where('user_id', $user->id)->latest()->get();
        
        // Append full image_url so the Flutter app can display the design image
        $orders->transform(function ($order) {
            if ($order->image_path) {
                $order->image_url = image_url($order->image_path);
            } else {
                $order->image_url = null;
            }
            return $order;
        });
        
        return response()->json(['data' => $orders]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'description' => 'required|string',
            'budget'      => 'nullable|numeric',
            'purity'      => 'nullable|string',
            'weight'      => 'nullable|numeric',
            'merchant_id' => 'required|exists:merchants,id',
            'image'       => 'nullable|image|max:5120',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $data = $request->only(['description', 'budget', 'purity', 'weight', 'merchant_id']);
        $data['user_id'] = $request->user()->id;
        $data['status']  = 'pending';

        if ($request->hasFile('image')) {
            $data['image_path'] = $request->file('image')->store('custom_designs', 'public');
        }

        $order = CustomDesignOrder::create($data);

        // Notify merchant via WebSocket (Wrapped in try-catch to prevent 500 error on socket failure)
        try {
            $merchant = Merchant::with('user')->find($data['merchant_id']);
            if ($merchant && $merchant->user) {
                $payload = [
                    'title'    => 'طلب تصميم خاص جديد 🎨',
                    'message'  => 'لديك طلب تصميم جديد من أحد العملاء',
                    'type'     => 'custom_design_new',
                    'order_id' => $order->id,
                ];

                // 1. Save to Database for persistence in notification screen
                $notification = \App\Models\Notification::create([
                    'user_id' => $merchant->user->id,
                    'notifiable_id' => $merchant->user->id,
                    'notifiable_type' => get_class($merchant->user),
                    'type'    => 'custom_design_new',
                    'title'   => $payload['title'],
                    'message' => $payload['message'],
                    'data'    => $payload,
                    'priority' => 'high',
                ]);

                // 2. Real-time push (Include the DB ID so the client can handle it as a real record)
                $payload['id'] = $notification->id;
                $payload['created_at'] = $notification->created_at->toIso8601String();
                
                broadcast(new NewNotificationEvent($merchant->user->id, $payload));
            }
        } catch (\Exception $e) {
            \Log::error("Notification failed for custom design: " . $e->getMessage());
        }

        return response()->json([
            'message' => 'تم إرسال طلب التصميم بنجاح',
            'data'    => $order
        ], 201);
    }
}
