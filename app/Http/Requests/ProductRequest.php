<?php
  
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'description_lines' => 'nullable|array',
            'description_lines.*' => 'nullable|string|max:500',
            'variants' => 'required|array|min:1',
            'variants.*.sku' => 'nullable|string|max:100',
            'variants.*.price' => ['required', 'numeric', 'min:0'],
            'variants.*.attributes' => 'nullable|array',
            'variants.*.attributes.*' => 'exists:attribute_values,id',
            'discount.value' => 'nullable|numeric|min:0',
            'image' => 'nullable|image|max:2048',
            'is_recommended' => 'nullable',
            'is_featured' => 'nullable',
            'active' => 'nullable'
        ];
    }
}
