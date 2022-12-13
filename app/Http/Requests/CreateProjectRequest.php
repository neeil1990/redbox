<?php

namespace App\Http\Requests;

use App\Rules\UniqueProjectName;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

/**
 * @property string project_name
 * @property mixed project_id
 * @property mixed description
 * @property mixed short_description
 */
class CreateProjectRequest extends FormRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'project_name' => ['required', new UniqueProjectName()],
            'description' => 'required|min:10|max:4294967295',
            'short_description' => 'max:100'
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
            'short_description.required' => __('The text cannot be empty'),
            'description.required' => __('The text cannot be empty'),
            'description.min' => __('The text must contain at least 10 characters'),
            'description.max' => __('The text must contain no more than 4294967295 characters'),
            'short_description.max' => __('The short description must contain a maximum of 100 characters'),
        ];
    }
}
