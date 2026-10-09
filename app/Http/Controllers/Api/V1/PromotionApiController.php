<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Promotion;
use Illuminate\Http\Request;

class PromotionApiController extends Controller
{
    public function index(Request $request)
    {
        $placement = $request->query('placement');
        $type = $request->query('type');

        $promotions = Promotion::active()
            ->with(['merchant', 'category', 'product'])
            ->when($placement, function ($q) use ($placement) {
                return $q->where('placement', $placement);
            })
            ->when($type, function ($q) use ($type) {
                return $q->where('type', $type);
            })
            ->orderBy('priority', 'desc')
            ->get();

        return $this->success($promotions);
    }
}
