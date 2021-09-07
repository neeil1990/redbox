<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

/**
 * @property string project_name
 * @property string description
 * @property string id
 * @property mixed short_description
 */
class EditProjectRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'project_name' => 'required|min:2',
            'short_description' => 'required|min:3|max:100'
        ];
    }

    /**
     * @return string[]
     */
    public function messages(): array
    {
        return [
            'project_name.required' => __('The project name cannot be empty'),
            'project_name.min' => __('The project name must contain at least 2 characters'),
            'project_name.unique' => __('A project with this name already exists'),
            'short_description.required' => __('This field cannot be empty'),
            'short_description.min' => __('The text must contain at least 10 characters'),
        ];
    }

}
