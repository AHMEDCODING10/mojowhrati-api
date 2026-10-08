<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\AppUpdate;
use Illuminate\Http\Request;

class AppUpdateApiController extends Controller
{
    public function latest(Request $request)
    {
        $role = $request->query('role', 'customer');

        $update = AppUpdate::where('is_sent', true)
            ->where(function($q) use ($role) {
                $q->where('target_audience', 'all')
                  ->orWhere('target_audience', $role);
            })
            ->latest('id')
            ->first();

        if (!$update) {
            return response()->json([
                'status' => 'success',
                'has_update' => false,
                'data' => null
            ]);
        }

        return response()->json([
            'status' => 'success',
            'has_update' => true,
            'data' => [
                'id' => $update->id,
                'version_number' => $update->version_number,
                'release_notes' => $update->release_notes,
                'target_audience' => $update->target_audience,
                'download_url' => $update->download_url,
                'play_store_url' => $update->play_store_url,
                'is_force_update' => $update->is_force_update,
                'published_at' => $update->created_at->toIso8601String(),
            ]
        ]);
    }
}
