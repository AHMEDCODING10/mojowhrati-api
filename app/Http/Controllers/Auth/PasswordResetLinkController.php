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
            'email.email'    => 'يرجى إدخال عنوان بريد إلكتروني صحيح.',
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

        // 3. Cache code for 15 minutes (keyed by email)
        Cache::put('web_password_reset_code_' . $email, $code, now()->addMinutes(15));
        session(['web_reset_email' => $email]);

        // 4. Send email with OTP — show clear error if mail fails
        try {
            Mail::to($email)->send(new VerificationCode($code, 'كود استعادة كلمة المرور'));
        } catch (\Exception $e) {
            \Log::error('[PasswordReset] Mail send failed for ' . $email . ': ' . $e->getMessage());

            // Clear the cached code so it cannot be guessed without a sent email
            Cache::forget('web_password_reset_code_' . $email);
            session()->forget('web_reset_email');

            throw ValidationException::withMessages([
                'email' => ['حدث خطأ أثناء إرسال البريد الإلكتروني. يرجى المحاولة مرة أخرى لاحقاً.'],
            ]);
        }

        return redirect()->route('password.verify-code.show')
                         ->with('status', 'تم إرسال كود التحقق إلى بريدك الإلكتروني.');
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
