<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rules\Password;

class ResetPasswordRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'email' => ['required', 'string', 'email:rfc,dns'],
            'phone' => ['required', 'string', 'regex:/^(70|71|73|77|78)\d{7}$/'],
            'password' => [
                'required', 
                'string', 
                'confirmed', 
                Password::min(8)->letters()->mixedCase()->numbers()->symbols()
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'email.required' => 'يرجى إدخال البريد الإلكتروني.',
            'email.email' => 'صيغة البريد الإلكتروني غير صحيحة.',
            'phone.required' => 'يرجى إدخال رقم الهاتف.',
            'phone.regex' => 'يرجى إدخال رقم يمني صحيح (مثال: 77XXXXXXX).',
            'password.required' => 'يرجى إدخال كلمة المرور.',
            'password.confirmed' => 'كلمة المرور غير متطابقة.',
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
