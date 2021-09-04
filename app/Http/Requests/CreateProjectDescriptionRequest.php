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
            'description' => 'required|min:10'
        ];
    }

    /**
     * @return string[]
     */
    public function messages(): array
    {
        return [
            'project_id.required' => 'Название проекта не может быть пустым',
            'description.required'  => 'Описание проекта не может быть пустым',
            'description.min'  => 'Описание должно содержать минимум 10 символов',
        ];
    }
}
