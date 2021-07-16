<?php

namespace App\Http\Requests;

use App\DTO\UserRegisterOldDTO;
use Illuminate\Foundation\Http\FormRequest;

class UserRegisterRequest extends FormRequest
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
            'name' => 'required|string',
            'email' => 'required|string|email',
            'admin' => 'boolean'
        ];
    }

    public function getDTO()
    {
        return new UserRegisterOldDTO(
            $this->get('name'),
            $this->get('email'),
            $this->boolean('admin')
        );
    }
}
