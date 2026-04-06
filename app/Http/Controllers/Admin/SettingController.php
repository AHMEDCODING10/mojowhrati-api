<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Setting;

class SettingController extends Controller
{
    public function index()
    {
        $settings = Setting::all()->groupBy('group');
        return view('settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $data = $request->except('_token', 'group');
        $group = $request->get('group', 'general');

        foreach ($data as $key => $value) {
            Setting::set($key, $value, $group);
        }

        return redirect()->back()->with('success', 'تم حفظ الإعدادات بنجاح');
    }

    public function changeLocale($locale)
    {
        if (in_array($locale, ['ar', 'en'])) {
            session(['locale' => $locale]);
        }
        return redirect()->back();
    }

    public function toggleTheme(Request $request)
    {
        $theme = $request->get('theme', 'dark');
        session(['theme' => $theme]);
        return response()->json(['success' => true]);
    }
}
