<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

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
            'project_name' => 'required|min:2|unique:projects',
            'description' => 'required|min:10',
            'short_description' => 'max:100'
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
            'description.required'  => 'Описание проекта не может быть пустым',
            'description.min'  => 'Описание должно содержать минимум 10 символов',
            'short_description.max'  => 'Краткое описание должно содержать максимум 100 символов',
        ];
    }
}
