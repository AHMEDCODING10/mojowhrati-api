<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class NewPasswordController extends Controller
{
    /**
     * Display Step 3: New Password View.
     */
    public function create(Request $request): View|RedirectResponse
    {
        $email = session('web_reset_email');
        $verified = session('web_reset_code_verified');

        if (!$email || !$verified) {
            return redirect()->route('password.request');
        }

        return view('auth.reset-password', ['email' => $email]);
    }

    /**
     * Store the new password in DB and redirect to login.
     */
    public function store(Request $request): RedirectResponse
    {
        $email = session('web_reset_email');
        $verified = session('web_reset_code_verified');

        if (!$email || !$verified) {
            return redirect()->route('password.request');
        }

        $request->validate([
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ], [
            'password.required' => 'يرجى إدخال كلمة المرور الجديدة.',
            'password.min' => 'كلمة المرور يجب أن لا تقل عن 6 أحرف.',
            'password.confirmed' => 'كلمة المرور وتأكيد كلمة المرور غير متطابقين.',
        ]);

        $user = User::where('email', $email)->firstOrFail();
        $user->password = Hash::make($request->password);
        $user->save();

        // Clear reset cache and session variables
        Cache::forget('web_password_reset_code_' . $email);
        session()->forget(['web_reset_email', 'web_reset_code_verified']);

        return redirect()->route('login')->with('status', 'تم تغيير كلمة المرور بنجاح! يمكنك الآن تسجيل الدخول بكلمة المرور الجديدة.');
    }
}
