<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ReviewRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $isUpdate = $this->isMethod('PUT') || $this->isMethod('PATCH');

        return [
            'product_id' => $isUpdate ? 'sometimes|exists:products,id' : 'required|exists:products,id',
            'title' => 'required|string|max:255',
            'content' => 'required|string|min:10|max:2000',
            'overall_rating' => 'required|numeric|min:1|max:5',
            'ratings' => 'sometimes|array',
            'ratings.*' => 'numeric|min:1|max:5',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'product_id.required' => 'Please select a product to review.',
            'product_id.exists' => 'The selected product does not exist.',
            'title.required' => 'Please provide a review title.',
            'title.max' => 'The title may not be greater than 255 characters.',
            'content.required' => 'Please provide review content.',
            'content.min' => 'The review content must be at least 10 characters.',
            'content.max' => 'The review content may not be greater than 2000 characters.',
            'overall_rating.required' => 'Please provide an overall rating.',
            'overall_rating.numeric' => 'The rating must be a number.',
            'overall_rating.min' => 'The rating must be at least 1.',
            'overall_rating.max' => 'The rating may not be greater than 5.',
            'ratings.array' => 'Ratings must be provided as an array.',
            'ratings.*.numeric' => 'Each rating must be a number.',
            'ratings.*.min' => 'Each rating must be at least 1.',
            'ratings.*.max' => 'Each rating may not be greater than 5.',
        ];
    }

    /**
     * Configure the validator instance.
     *
     * @param  \Illuminate\Validation\Validator  $validator
     * @return void
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            if ($this->has('ratings')) {
                $criteriaIds = \App\Models\ReviewCriterion::active()->pluck('id')->toArray();
                
                foreach ($this->input('ratings', []) as $criterionId => $rating) {
                    if (!in_array($criterionId, $criteriaIds)) {
                        $validator->errors()->add("ratings.{$criterionId}", 'Invalid review criterion.');
                    }
                }
            }
        });
    }
}
