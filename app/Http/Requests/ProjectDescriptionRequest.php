<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @property mixed description
 * @property mixed description_id
 */
class ProjectDescriptionRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'description' => 'required|min:10'
        ];
    }

    /**
     * @return string[]
     */
    public function messages(): array
    {
        return [
            'description.required' => __('This field cannot be empty'),
            'description.min' => __('The description must contain at least 10 characters'),
        ];
    }
}
