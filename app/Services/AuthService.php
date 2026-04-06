<?php

namespace App\Services;

use App\Models\User;
use App\Models\Merchant;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Mail;
use App\Mail\VerificationCode;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class AuthService
{
    public function register(array $data)
    {
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'],
            'password' => Hash::make($data['password']),
            'role' => $data['role'] ?? 'customer',
        ]);
        
        if ($user->role === 'merchant') {
            Merchant::create([
                'user_id' => $user->id,
                'store_name' => $data['store_name'] ?? $user->name,
                'contact_number' => $user->phone,
                'store_status' => 'active',
                'approved' => false, // Needs admin approval if required
            ]);
        }

        // Notify Admins
        try {
            app(\App\Services\NotificationService::class)->notifyAdmins(
                'new_user',
                'عضو جديد انضم إلينا',
                "قام {$user->name} بإنشاء حساب جديد بصفة " . ($user->role === 'merchant' ? 'تاجر' : 'عميل'),
                [
                    'user_id' => $user->id,
                    'user_name' => $user->name,
                    'role' => $user->role,
                    'icon' => 'user-plus'
                ],
                true // Consolidate user joins
            );
        } catch (\Exception $e) {
            Log::error("Failed to notify admins of new user: " . $e->getMessage());
        }

        return $user->load('merchant');
    }

    public function login(string $phone, string $password)
    {
        $user = User::where('phone', $phone)->first();

        if (!$user || !Hash::check($password, $user->password)) {
            throw ValidationException::withMessages([
                'phone' => ['بيانات الاعتماد غير صحيحة.'],
            ]);
        }
        
        // Status check for blocked users
        if ($user->status === 'blocked') {
            throw ValidationException::withMessages([
                'phone' => ['account_blocked'],
            ]);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return [
            'user' => $user->load('merchant'),
            'token' => $token,
        ];
    }

    public function updateProfile($user, array $data)
    {
        if (isset($data['name'])) {
            $user->name = $data['name'];
        }
        if (isset($data['phone'])) {
            $user->phone = $data['phone'];
        }
        if (isset($data['password']) && !empty($data['password'])) {
            $user->password = Hash::make($data['password']);
        }
        
        
        $user->save();
        return $user;
    }

    public function resetPassword($email, $phone, $newPassword)
    {
        $user = User::where('email', $email)->where('phone', $phone)->first();

        if (!$user) {
            throw new \Exception('البيانات غير متطابقة مع أي حساب مسجل.');
        }

        $user->password = Hash::make($newPassword);
        $user->save();

        return true;
    }

    public function sendResetCode($email)
    {
        $user = User::where('email', $email)->first();
        if (!$user) {
            throw new \Exception('البريد الإلكتروني غير مسجل لدينا.');
        }

        $code = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        
        Cache::put('password_reset_' . $email, $code, now()->addMinutes(10));

        Log::info("Password reset code for $email: $code");

        // Send the real email
        try {
            Mail::to($user->email)->send(new VerificationCode($code, 'رمز استعادة كلمة المرور'));
        } catch (\Exception $e) {
            Log::error("Failed to send reset email to $email: " . $e->getMessage());
        }

        return $code; 
    }

    public function sendVerificationCode($email, $title = 'رمز التحقق الخاص بك')
    {
        $code = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        
        Cache::put('verification_code_' . $email, $code, now()->addMinutes(10));

        Log::info("Verification code for $email: $code");

        try {
            Mail::to($email)->send(new VerificationCode($code, $title));
        } catch (\Exception $e) {
            Log::error("Failed to send verification email to $email: " . $e->getMessage());
        }

        return $code;
    }

    public function verifyResetCode($email, $code)
    {
        $cachedCode = Cache::get('password_reset_' . $email);
        
        if (!$cachedCode || $cachedCode !== $code) {
            throw new \Exception('رمز التحقق غير صحيح أو انتهت صلاحيته.');
        }
        return true;
    }

    public function resetPasswordWithCode($email, $code, $password)
    {
        $this->verifyResetCode($email, $code);

        $user = User::where('email', $email)->first();
        if (!$user) {
            throw new \Exception('المستخدم غير موجود.');
        }

        $user->password = Hash::make($password);
        $user->save();

        // Remove code from cache after success
        Cache::forget('password_reset_' . $email);

        return true;
    }

    public function changePassword($user, string $currentPassword, string $newPassword)
    {
        if (!Hash::check($currentPassword, $user->password)) {
            throw new \Exception('كلمة المرور الحالية غير صحيحة.');
        }

        $user->password = Hash::make($newPassword);
        $user->save();

        return true;
    }

    public function deleteAccount(User $user)
    {
        // Delete merchant profile if exists
        if ($user->merchant) {
            $user->merchant->delete();
        }
        
        $user->tokens()->delete(); // Revoke all tokens
        $user->delete();

        return true;
    }
}
