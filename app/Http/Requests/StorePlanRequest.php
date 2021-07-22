<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePlanRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'file' => 'required|file|mimes:zip',
            'plan_id' => 'required',
        ];
    }

    public function messages()
    {
        return [
            'file.required' => 'Необходимо загрузить архив',
            'plan_id.required' => 'Поле не может оставаться пустым',
        ];
    }
}
