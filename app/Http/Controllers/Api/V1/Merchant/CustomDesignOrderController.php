<?php

namespace App\Http\Controllers\Api\V1\Merchant;

use App\Http\Controllers\Controller;
use App\Models\CustomDesignOrder;
use Illuminate\Http\Request;
use App\Traits\ApiResponse;

class CustomDesignOrderController extends Controller
{
    use ApiResponse;

    public function index(Request $request)
    {
        $merchant = $request->user()->merchant;
        if (!$merchant) {
            return $this->error('Merchant profile not found', 404);
        }

        $orders = CustomDesignOrder::with('user')
            ->where('merchant_id', $merchant->id)
            ->latest()
            ->get()
            ->map(function ($order) {
                $arr = $order->toArray();
                $arr['image_url'] = $order->image_path
                    ? \image_url($order->image_path)
                    : null;
                return $arr;
            });

        return $this->success($orders);
    }

    public function updateStatus(Request $request, $id)
    {
        $merchant = $request->user()->merchant;
        if (!$merchant) {
            return $this->error('Merchant profile not found', 404);
        }

        $order = CustomDesignOrder::where('id', $id)
            ->where('merchant_id', $merchant->id)
            ->firstOrFail();

        $request->validate([
            'status' => 'required|in:pending,reviewed,contacted,completed,rejected'
        ]);

        $order->update(['status' => $request->status]);

        // Send Notification to Customer
        $customer = $order->user;
        if ($customer) {
            $customer->notify(new \App\Notifications\CustomDesignStatusNotification($order));
            
            // Real-time broadcast
            broadcast(new \App\Events\NewNotificationEvent($customer->id, [
                'title' => 'تحديث على طلب التصميم الخاص',
                'message' => 'تم تحديث حالة طلب التصميم الخاص بك.',
                'type' => 'custom_design_status',
                'order_id' => $order->id,
                'status' => $request->status,
            ]));
        }

        return $this->success($order, 'Status updated successfully');
    }
}
