<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Services\AuthService;
use App\Http\Requests\Api\V1\RegisterRequest;
use App\Http\Requests\Api\V1\LoginRequest;
use App\Http\Requests\Api\V1\UpdateProfileRequest;
use App\Http\Requests\Api\V1\ResetPasswordRequest;
use App\Http\Requests\Api\V1\ChangePasswordRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    protected $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    public function register(RegisterRequest $request)
    {
        try {
            $user = $this->authService->register($request->validated());
            $token = $user->createToken('auth_token')->plainTextToken;

            return $this->success([
                'user' => $user,
                'token' => $token,
            ], 'تم إنشاء الحساب بنجاح', 201);
        } catch (\Exception $e) {
            return $this->error('خطأ في عملية التسجيل: ' . $e->getMessage(), 400);
        }
    }

    public function login(LoginRequest $request)
    {
        try {
            $result = $this->authService->login($request->phone, $request->password);
            return $this->success($result, 'تم تسجيل الدخول بنجاح');
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), 401);
        }
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return $this->success(null, 'تم تسجيل الخروج بنجاح');
    }

    public function updateProfile(UpdateProfileRequest $request)
    {
        $user = $request->user();
        $user = $this->authService->updateProfile($user, $request->all());

        return $this->success(['user' => $user], 'تم تحديث الملف الشخصي بنجاح');
    }

    public function resetPassword(ResetPasswordRequest $request)
    {
        try {
            $this->authService->resetPassword(
                $request->email,
                $request->phone,
                $request->password
            );
            return $this->success(null, 'تم تغيير كلمة المرور بنجاح');
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), 422);
        }
    }

    public function sendResetCode(Request $request)
    {
        $request->validate(['email' => 'required|email']);
        try {
            $this->authService->sendResetCode($request->email);
            return $this->success(null, 'تم إرسال رمز التحقق بنجاح');
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), 422);
        }
    }

    public function verifyResetCode(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'code' => 'required|string|size:6'
        ]);
        try {
            $this->authService->verifyResetCode($request->email, $request->code);
            return $this->success(null, 'تم التحقق من الكود بنجاح');
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), 422);
        }
    }

    public function resetPasswordWithCode(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'code' => 'required|string|size:6',
            'password' => 'required|string|min:8|confirmed',
        ]);
        try {
            $this->authService->resetPasswordWithCode($request->email, $request->code, $request->password);
            return $this->success(null, 'تم إعادة تعيين كلمة المرور بنجاح');
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), 422);
        }
    }

    public function changePassword(ChangePasswordRequest $request)
    {
        try {
            $this->authService->changePassword($request->user(), $request->current_password, $request->new_password);
            return $this->success(null, 'تم تغيير كلمة المرور بنجاح');
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), 422);
        }
    }

    public function destroy(Request $request)
    {
        try {
            $user = $request->user();
            $this->authService->deleteAccount($user);
            return $this->success(null, 'تم حذف الحساب بنجاح');
        } catch (\Exception $e) {
            return $this->error('حدث خطأ أثناء حذف الحساب: ' . $e->getMessage(), 500);
        }
    }
}
