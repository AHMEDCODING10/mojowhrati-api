<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Mail\VerificationCode;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class PasswordResetLinkController extends Controller
{
    /**
     * Display the forgot password view (Step 1).
     */
    public function create(): View
    {
        return view('auth.forgot-password');
    }

    /**
     * Handle sending 6-digit OTP code to user email.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'email'],
        ], [
            'email.required' => 'يرجى إدخال البريد الإلكتروني.',
            'email.email' => 'يرجى إدخال عنوان بريد إلكتروني صحيح.',
        ]);

        $email = trim($request->email);

        // 1. Check if email exists in database
        $user = User::where('email', $email)->first();
        if (!$user) {
            throw ValidationException::withMessages([
                'email' => ['البريد الإلكتروني المدخل غير مسجل في النظام لدينا.'],
            ]);
        }

        // 2. Generate 6-digit random code
        $code = (string) rand(100000, 999999);

        // 3. Cache code for 15 minutes
        Cache::put('web_password_reset_code_' . $email, $code, now()->addMinutes(15));
        session(['web_reset_email' => $email]);

        // 4. Send luxury email with 6-digit code
        try {
            Mail::to($email)->send(new VerificationCode($code, 'كود استعادة كلمة المرور'));
        } catch (\Exception $e) {
            \Log::error('Failed to send password reset email: ' . $e->getMessage());
        }

        return redirect()->route('password.verify-code.show');
    }

    /**
     * Display Step 2: Verification Code View.
     */
    public function showVerifyCode(): View|RedirectResponse
    {
        $email = session('web_reset_email');
        if (!$email) {
            return redirect()->route('password.request');
        }

        return view('auth.verify-code', ['email' => $email]);
    }

    /**
     * Handle 6-digit OTP code verification.
     */
    public function verifyCode(Request $request): RedirectResponse
    {
        $request->validate([
            'code' => ['required', 'string', 'min:6', 'max:6'],
        ], [
            'code.required' => 'يرجى إدخال كود التحقق المكون من 6 أرقام.',
            'code.min' => 'كود التحقق يتكون من 6 أرقام.',
            'code.max' => 'كود التحقق يتكون من 6 أرقام.',
        ]);

        $email = session('web_reset_email');
        if (!$email) {
            return redirect()->route('password.request');
        }

        $cachedCode = Cache::get('web_password_reset_code_' . $email);
        $userCode = trim($request->code);

        if (!$cachedCode || $cachedCode !== $userCode) {
            throw ValidationException::withMessages([
                'code' => ['كود التحقق غير صحيح أو انتهت صلاحيته. يرجى التأكد وإعادة المحاولة.'],
            ]);
        }

        // Code verified successfully
        session(['web_reset_code_verified' => true]);

        return redirect()->route('password.reset-form.show');
    }
}
