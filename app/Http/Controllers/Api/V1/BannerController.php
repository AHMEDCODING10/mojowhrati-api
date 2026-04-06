<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use Illuminate\Http\Request;

class BannerController extends Controller
{
    public function index(Request $request)
    {
        $now = now();
        $placement = $request->query('placement');

        $banners = Banner::where('is_active', true)
            ->when($placement, function ($query) use ($placement) {
                return $query->where('placement', $placement);
            })
            ->whereIn('target', ['all', 'customer'])
            ->where(function ($query) use ($now) {
                $query->whereNull('start_at')
                      ->orWhere('start_at', '<=', $now);
            })
            ->where(function ($query) use ($now) {
                $query->whereNull('end_at')
                      ->orWhere('end_at', '>=', $now);
            })
            ->orderBy('position', 'asc')
            ->orderBy('created_at', 'desc')
            ->get();

        return $this->success($banners);
    }
}
