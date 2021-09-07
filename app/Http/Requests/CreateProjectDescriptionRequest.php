<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @property int project_id
 * @property text description
 */
class CreateProjectDescriptionRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'project_id' => 'required',
            'description' => 'required|min:10|max:4294967295',
        ];
    }

    /**
     * @return string[]
     */
    public function messages(): array
    {
        return [
            'project_name.required' => __('The project name cannot be empty'),
            'description.required' => __('This field cannot be empty'),
            'description.min' => __('The text must contain at least 10 characters'),
            'description.max' => __('The text must contain no more than 10 characters'),
        ];
    }
}
