<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreFoodRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() === true;
    }

    /**
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [
            'category_id' => ['required', 'integer', 'exists:categories,id'],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
            'price' => ['required', 'numeric', 'min:0', 'max:999999999'],
            'image' => ['nullable', 'string', 'max:2048'],
            'is_available' => ['nullable', 'boolean'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'category_id.required' => 'Vui lòng chọn danh mục cho món ăn.',
            'category_id.exists' => 'Danh mục đã chọn không tồn tại.',
            'name.required' => 'Vui lòng nhập tên món ăn.',
            'price.required' => 'Vui lòng nhập giá món ăn.',
            'price.min' => 'Giá món ăn không được âm.',
        ];
    }
}
