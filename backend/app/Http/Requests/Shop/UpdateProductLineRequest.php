<?php

namespace App\Http\Requests\Shop;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProductLineRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $id = $this->route('id');
        return [
            'category_id' => 'integer|exists:categories,id',
            'name' => 'string|max:255',
            'slug' => 'string|max:255|unique:product_lines,slug,' . $id,
            'description' => 'nullable|string',
            'short_description' => 'nullable|string|max:500',
            'color_name' => 'nullable|string|max:100',
            'color_code' => 'nullable|string|max:50',
            'base_ingredient' => 'nullable|string|max:255',
            'effet' => 'nullable|string|max:255',
            'skin_type' => 'nullable|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'is_active' => 'boolean',
            'sort_order' => 'integer|min:0',
        ];
    }
}
