<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\CustomDesignOrder;
use App\Models\Merchant;
use App\Services\ImageKitService;
use Illuminate\Support\Facades\Validator;
use App\Events\NewNotificationEvent;

use App\Http\Resources\Api\V1\CustomDesignResource;

class CustomDesignOrderController extends Controller
{
    protected $imageKitService;

    public function __construct(ImageKitService $imageKitService)
    {
        $this->imageKitService = $imageKitService;
    }
    
    public function index(Request $request)
    {
        $user = $request->user();
        $orders = CustomDesignOrder::with('merchant')->where('user_id', $user->id)->latest()->get();
        
        return $this->success(CustomDesignResource::collection($orders));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'description' => 'required|string',
            'budget'      => 'nullable|string', // Changed to string to handle empty inputs safely, then cast manually
            'purity'      => 'nullable|string',
            'weight'      => 'nullable|string', // Changed to string
            'merchant_id' => 'required|exists:merchants,id',
            'reference_image' => 'nullable|image|max:10240', // Increased size to 10MB
        ]);

        if ($validator->fails()) {
            return $this->error('بيانات غير صالحة', 422, $validator->errors());
        }

        $data = [
            'description' => $request->description,
            'budget'      => $request->budget ?: null,
            'purity'      => $request->purity,
            'weight'      => $request->weight ?: null,
            'merchant_id' => $request->merchant_id,
            'user_id'     => $request->user()->id,
            'status'      => 'pending',
        ];

        if ($request->hasFile('reference_image')) {
            $data['image_path'] = $this->imageKitService->upload($request->file('reference_image'));
        }

        $order = CustomDesignOrder::create($data);
        $order->load(['merchant', 'user']);

        // Notify merchant via WebSocket
        try {
            $merchant = $order->merchant;
            if ($merchant && $merchant->user) {
                $payload = [
                    'title'    => 'طلب تصميم خاص جديد 🎨',
                    'message'  => 'لديك طلب تصميم جديد من أحد العملاء',
                    'type'     => 'custom_design_new',
                    'order_id' => $order->id,
                ];

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

                $payload['id'] = $notification->id;
                $payload['created_at'] = $notification->created_at->toIso8601String();
                
                broadcast(new NewNotificationEvent($merchant->user->id, $payload));
            }
        } catch (\Exception $e) {
            \Log::error("Notification failed for custom design: " . $e->getMessage());
        }

        return $this->success(new CustomDesignResource($order), 'تم إرسال طلب التصميم بنجاح', 201);
    }
}
