<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rules\Password;

class RegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255', 'regex:/^[a-zA-Z\p{L}\s]+$/u'],
            'phone' => ['required', 'string', 'regex:/^(70|71|73|77|78)\d{7}$/', 'unique:users,phone'],
            'email' => ['required', 'string', 'email:rfc,dns', 'unique:users,email'],
            'password' => [
                'required', 
                'string', 
                'confirmed', 
                Password::min(8)->letters()->mixedCase()->numbers()->symbols()
            ],
            'role' => ['nullable', 'string', 'in:customer,merchant'],
            'store_name' => ['required_if:role,merchant', 'string', 'max:255', 'regex:/^[a-zA-Z\p{L}\s]+$/u'],
            'commercial_register' => ['nullable', 'string', 'max:255'],
            'whatsapp_number' => ['nullable', 'string', 'max:20'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'يرجى إدخال الاسم.',
            'name.regex' => 'يرجى كتابة الاسم باستخدام الحروف فقط دون أرقام أو رموز.',
            'phone.required' => 'يرجى إدخال رقم الهاتف.',
            'phone.regex' => 'يرجى إدخال رقم يمني صحيح (مثال: 77XXXXXXX).',
            'phone.unique' => 'رقم الهاتف مسجل مسبقاً، يرجى تسجيل الدخول.',
            'email.required' => 'يرجى إدخال البريد الإلكتروني.',
            'email.email' => 'صيغة البريد الإلكتروني غير صحيحة.',
            'email.unique' => 'الإيميل مسجل مسبقاً، يرجى تسجيل الدخول.',
            'password.required' => 'يرجى إدخال كلمة المرور.',
            'password.confirmed' => 'كلمة المرور غير متطابقة.',
            'store_name.required_if' => 'يرجى إدخال اسم المتجر كتاجر.',
            'store_name.regex' => 'يرجى كتابة اسم المتجر باستخدام الحروف فقط دون أرقام أو رموز.',
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
