<?php

namespace App\Http\Requests\Shop;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $id = $this->route('id');
        return [
            'product_line_id' => 'nullable|integer|exists:product_lines,id',
            'category_id' => 'integer|exists:categories,id',
            'name' => 'string|max:255',
            'slug' => 'string|max:255|unique:products,slug,' . $id,
            'description' => 'nullable|string',
            'bienfaits' => 'nullable|string',
            'composition' => 'nullable|string',
            'conseils_utilisation' => 'nullable|string',
            'skin_type' => 'nullable|string|max:255',
            'effet' => 'nullable|string|max:255',
            'is_active' => 'boolean',
            'sort_order' => 'integer|min:0',
        ];
    }
}
