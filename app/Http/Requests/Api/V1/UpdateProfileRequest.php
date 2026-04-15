<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rules\Password;

class UpdateProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $userId = $this->user() ? $this->user()->id : null;

        return [
            'name' => ['sometimes', 'string', 'max:255', 'regex:/^[a-zA-Z\p{L}\s]+$/u'],
            'phone' => ['sometimes', 'string', 'regex:/^(70|71|73|77|78)\d{7}$/', "unique:users,phone,{$userId}"],
            'password' => [
                'sometimes', 
                'string', 
                'confirmed', 
                Password::min(8)->letters()->mixedCase()->numbers()->symbols()
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'name.regex' => 'يرجى كتابة الاسم باستخدام الحروف فقط دون أرقام أو رموز.',
            'phone.regex' => 'يرجى إدخال رقم يمني صحيح (مثال: 77XXXXXXX).',
            'phone.unique' => 'رقم الهاتف مسجل مسبقاً لحساب آخر.',
            'password.confirmed' => 'كلمة المرور غير متطابقة.',
            'password.min' => 'كلمة المرور ضعيفة. يجب أن تحتوي على 8 رموز على الأقل.',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'success' => false,
            'message' => 'يرجى التحقق من المدخلات',
            'errors' => $validator->errors()
        ], 422));
    }
}
