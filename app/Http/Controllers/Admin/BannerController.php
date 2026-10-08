<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BannerController extends Controller
{
    public function index()
    {
        $banners = Banner::orderBy('position')->get();
        return view('banners.index', compact('banners'));
    }

    public function create()
    {
        return view('banners.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'type' => 'required|string|in:image,video',
            'image' => 'required_if:type,image|nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
            'video_url' => 'required_if:type,video|nullable|url',
            'target' => 'required|string|in:all,customer',
            'placement' => 'required|string|in:HOME_TOP,HOME_SLIDER,HOME_MIDDLE,PRODUCTS_CATALOG,SEARCH_VIEW',
            'link' => 'nullable|url',
            'position' => 'required|integer',
            'start_at' => 'nullable|date',
            'end_at' => 'nullable|date|after_or_equal:start_at',
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = app(\App\Services\ImageKitService::class)->upload($request->file('image'));
        }

        Banner::create([
            'title' => $request->title,
            'type' => $request->type,
            'image_url' => $imagePath,
            'video_url' => $request->video_url,
            'target' => $request->target,
            'placement' => $request->placement,
            'link' => $request->link,
            'position' => $request->position,
            'is_active' => true,
            'start_at' => $request->start_at,
            'end_at' => $request->end_at,
        ]);

        return redirect()->route('banners.index')->with('success', 'تم إضافة الإعلان بنجاح');
    }

    public function edit(Banner $banner)
    {
        return view('banners.edit', compact('banner'));
    }

    public function update(Request $request, Banner $banner)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'type' => 'required|string|in:image,video',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
            'video_url' => 'required_if:type,video|nullable|url',
            'target' => 'required|string|in:all,customer',
            'placement' => 'required|string|in:HOME_TOP,HOME_SLIDER,HOME_MIDDLE,PRODUCTS_CATALOG,SEARCH_VIEW',
            'link' => 'nullable|url',
            'position' => 'required|integer',
            'start_at' => 'nullable|date',
            'end_at' => 'nullable|date|after_or_equal:start_at',
        ]);

        $data = [
            'title' => $request->title,
            'type' => $request->type,
            'video_url' => $request->video_url,
            'target' => $request->target,
            'placement' => $request->placement,
            'link' => $request->link,
            'position' => $request->position,
            'start_at' => $request->start_at,
            'end_at' => $request->end_at,
        ];

        if ($request->hasFile('image')) {
            if ($banner->image_url && !str_starts_with($banner->image_url, 'http')) {
                Storage::disk('public')->delete($banner->image_url);
            }
            $data['image_url'] = app(\App\Services\ImageKitService::class)->upload($request->file('image'));
        }

        $banner->update($data);

        return redirect()->route('banners.index')->with('success', 'تم تحديث الإعلان بنجاح');
    }

    public function destroy(Banner $banner)
    {
        if ($banner->image_url && !str_starts_with($banner->image_url, 'http')) {
            Storage::disk('public')->delete($banner->image_url);
        }

        $banner->delete();

        return redirect()->route('banners.index')->with('success', 'تم حذف البنر بنجاح');
    }

    public function toggleStatus(Banner $banner)
    {
        $banner->update(['is_active' => !$banner->is_active]);
        return back()->with('success', 'تم تغيير حالة البنر');
    }
}
