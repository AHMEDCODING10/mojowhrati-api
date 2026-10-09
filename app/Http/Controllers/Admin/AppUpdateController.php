<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AppUpdate;
use App\Events\AppUpdateBroadcastEvent;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AppUpdateController extends Controller
{
    public function create()
    {
        return view('app_updates.create');
    }

    public function store(Request $request, NotificationService $notificationService)
    {
        $request->validate([
            'version_number' => 'required|string|max:50',
            'release_notes' => 'required|string',
            'target_audience' => 'required|in:all,customer,merchant',
            'apk_file' => 'nullable|file|mimes:apk,zip,bin|max:102400', // max 100MB
            'apk_url' => 'nullable|url',
            'play_store_url' => 'nullable|url',
            'scheduled_at' => 'nullable|date',
            'is_force_update' => 'nullable|boolean',
        ]);

        $apkFileUrl = $request->apk_url;

        if ($request->hasFile('apk_file')) {
            $file = $request->file('apk_file');
            $fileName = 'mojohrti_v' . str_replace('.', '_', $request->version_number) . '_' . time() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('public/builds', $fileName);
            $apkFileUrl = Storage::url($path);
        }

        $appUpdate = AppUpdate::create([
            'version_number' => $request->version_number,
            'release_notes' => $request->release_notes,
            'target_audience' => $request->target_audience,
            'apk_file_url' => $apkFileUrl,
            'play_store_url' => $request->play_store_url,
            'scheduled_at' => $request->scheduled_at ? now()->parse($request->scheduled_at) : now(),
            'is_force_update' => $request->boolean('is_force_update'),
            'is_sent' => true,
            'created_by' => auth()->id(),
        ]);

        // Dispatch real-time websocket broadcast to Flutter app clients
        try {
            broadcast(new AppUpdateBroadcastEvent($appUpdate));
        } catch (\Exception $e) {
            \Log::info('App update broadcast event log: ' . $e->getMessage());
        }

        // Send notifications inside system
        $audienceTitle = match($request->target_audience) {
            'customer' => 'تحديث جديد متاح للعملاء',
            'merchant' => 'تحديث جديد متاح للتجار',
            default => 'تحديث جديد متاح للنظام',
        };

        $notificationService->notifyAdmins(
            'system_update',
            $audienceTitle,
            "تم إصدار التحديث رقم ({$appUpdate->version_number}) وإرساله للمستخدمين.",
            ['version' => $appUpdate->version_number]
        );

        return redirect()->route('banners.index')->with('success', 'تم إضافة التحديث وإرساله بنجاح!');
    }

    public function destroy($id)
    {
        $update = AppUpdate::findOrFail($id);
        
        if ($update->apk_file_url && str_contains($update->apk_file_url, 'storage/builds')) {
            $relativePath = str_replace('/storage/', 'public/', $update->apk_file_url);
            Storage::delete($relativePath);
        }

        $update->delete();
        return back()->with('success', 'تم حذف التحديث بنجاح');
    }
}
