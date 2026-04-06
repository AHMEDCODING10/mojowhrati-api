<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Merchant;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MerchantController extends Controller
{
    use ApiResponse;

    public function index()
    {
        $merchants = Merchant::with('user')
            ->get()
            ->map(function (Merchant $m) {
                if (!$m->user) return null;
                return [
                    'id'          => (string) $m->id,
                    'store_name'  => $m->store_name ?? $m->user->name,
                    'store_logo'  => $m->logo ? \image_url($m->logo) : null,
                    'address'     => $m->store_description,
                    'whatsapp'    => $m->whatsapp_number,
                ];
            })
            ->filter()  // remove nulls
            ->values();

        return $this->success($merchants, 'تم جلب المتاجر بنجاح');
    }

    /**
     * Update the authenticated merchant's store identity (branding).
     */
    public function updateBranding(Request $request)
    {
        $user = $request->user();

        if ($user->role !== 'merchant' || !$user->merchant_id) {
            return $this->error('غير مصرح بالوصول', 403);
        }

        $request->validate([
            'store_name'        => 'sometimes|string|max:255',
            'store_description' => 'sometimes|string|max:1000',
            'whatsapp_number'   => 'sometimes|string|max:25',
            'instagram_handle'  => 'sometimes|string|max:100',
            'logo'              => 'sometimes|image|max:2048',
            'banner'            => 'sometimes|image|max:4096',
        ]);

        $merchant = Merchant::where('user_id', $user->id)->firstOrFail();

        // Handle logo upload
        if ($request->hasFile('logo')) {
            if ($merchant->logo) {
                Storage::disk('public')->delete($merchant->logo);
            }
            $merchant->logo = $request->file('logo')->store('merchants/logos', 'public');
        }

        // Handle banner upload
        if ($request->hasFile('banner')) {
            if ($merchant->banner) {
                Storage::disk('public')->delete($merchant->banner);
            }
            $merchant->banner = $request->file('banner')->store('merchants/banners', 'public');
        }

        // Update text fields
        $merchant->fill($request->only([
            'store_name', 'store_description', 'whatsapp_number', 'instagram_handle',
        ]));
        $merchant->save();

        // Return full merchant with URL helpers
        $merchant->refresh();
        $data = [
            'store_name'        => $merchant->store_name,
            'store_description' => $merchant->store_description,
            'whatsapp_number'   => $merchant->whatsapp_number,
            'instagram_handle'  => $merchant->instagram_handle,
            'logo_url'          => $merchant->logo_url,
            'banner_url'        => $merchant->banner ? \image_url($merchant->banner) : null,
        ];

        return $this->success($data, 'تم تحديث هوية المتجر بنجاح');
    }
}
