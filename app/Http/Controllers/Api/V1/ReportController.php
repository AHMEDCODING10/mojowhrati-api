<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Report;
use App\Models\Product;
use App\Models\Merchant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ReportController extends Controller
{
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'type' => 'required|in:product,merchant',
            'id' => 'required|integer',
            'reason' => 'required|string|max:1000',
        ]);

        if ($validator->fails()) {
            return $this->error('Validation Error', 422, $validator->errors());
        }

        $modelClass = $request->type === 'product' ? Product::class : Merchant::class;
        $target = $modelClass::find($request->id);

        if (!$target) {
            return $this->error('Target not found', 404);
        }

        $report = Report::create([
            'user_id' => $request->user()->id,
            'reportable_id' => $target->id,
            'reportable_type' => $modelClass,
            'reason' => $request->reason,
            'status' => 'pending',
        ]);

        return $this->success($report, 'تم إرسال البلاغ بنجاح');
    }
}
