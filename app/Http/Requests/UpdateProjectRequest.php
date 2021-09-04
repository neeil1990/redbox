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
class UpdateProjectRequest extends FormRequest
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
            'project_name.required' => 'Название проекта не может быть пустым',
            'project_name.min' => 'Название проекта должно содержать минимум 2 символа',
            'project_name.unique' => 'Проект с таким названием уже существует',
            'short_description.required'  => 'Описание проекта не может быть пустым',
            'short_description.min'  => 'Описание должно содержать минимум 10 символов',
        ];
    }

}
