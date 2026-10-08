<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreProductRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $user = $this->user();
        if (!$user) return false;

        // Allow merchants who have a merchant record and are verified
        if ($user->role === 'merchant' && $user->merchant_id !== null) {
            return $user->is_verified;
        }

        // Allow administrative staff
        return $user->isStaff();
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'weight' => 'required|numeric|min:0',
            'category_id' => 'required|exists:categories,id',
            'material_id' => 'nullable|exists:materials,id',
            'material_type' => 'required|string|in:gold,silver,gemstone',
            'purity' => 'nullable|string|max:20',
            'workmanship' => 'nullable|numeric|min:0',
            'stone_type' => 'nullable|string|max:255',
            'stone_weight' => 'nullable|numeric|min:0',
            'clarity' => 'nullable|string|max:255',
            'cut' => 'nullable|string|max:255',
            'type' => 'required|string',
            'is_featured' => 'nullable',
            'stock_quantity' => 'nullable|integer|min:0',
            'manage_stock' => 'nullable|boolean',
            'images' => 'nullable|array',
            'images.*' => 'image|mimes:jpeg,png,jpg,webp|max:5120',
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'يرجى إدخال عنوان المنتج.',
            'description.required' => 'يرجى إدخال وصف المنتج.',
            'weight.required' => 'يرجى إدخال وزن المنتج.',
            'weight.numeric' => 'الوزن يجب أن يكون رقماً.',
            'category_id.required' => 'يرجى اختيار القسم.',
            'category_id.exists' => 'القسم المختار غير موجود.',
            'material_type.required' => 'يرجى تحديد نوع المادة (ذهب، فضة، إلخ).',
            'material_type.in' => 'نوع المادة غير صالح.',
            'type.required' => 'يرجى تحديد نوع المنتج.',
            'stock_quantity.integer' => 'كمية المخزون يجب أن تكون رقماً صحيحاً.',
            'images.*.image' => 'يجب أن يكون الملف صورة.',
            'images.*.max' => 'حجم الصورة يجب أن لا يتجاوز 5 ميجابايت.',
        ];
    }

    /**
     * Handle a failed validation attempt.
     */
    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'success' => false,
            'message' => 'يرجى التحقق من بيانات المنتج',
            'errors' => $validator->errors()
        ], 422));
    }
}
