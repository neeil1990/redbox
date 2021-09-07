<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @property mixed description
 * @property mixed description_id
 */
class EditProjectDescriptionRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'description' => 'required|min:10|max:4294967295',
        ];
    }

    /**
     * @return string[]
     */
    public function messages(): array
    {
        return [
            'description.required' => __('The text cannot be empty'),
            'description.min' => __('The text must contain at least 10 characters'),
            'description.max' => __('The text must contain no more than 10 characters'),
        ];
    }
}
