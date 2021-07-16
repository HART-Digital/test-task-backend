<?php

namespace App\Http\Requests;

use App\DTO\UserRegisterDTO;
use Illuminate\Foundation\Http\FormRequest;

class RegistrationRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'email' => 'required|string|email|unique:users',
            'name' => 'required|string',
            'role' => 'required|array'
        ];
    }


    public function getDTO(): UserRegisterDTO
    {
        return new UserRegisterDTO(
            $this->only(
                'email',
                'name',
                'role'
            )
        );
    }
}
