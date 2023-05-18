<?php

namespace App\Http\Requests;

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
            'name' => 'required|min:2|max:30',
            'email' => 'required|string|email|max:50|unique:users',
            'lr_number' => 'required|alpha_dash|min:2|max:30|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'email.required' => 'Email обязателен для заполнения',
            'lr_number.required' => 'Номер LR обязателен для заполнения',
            'email.unique' => 'Email должен быть уникален',
            'lr_number.unique' => 'Номер LR должен быть уникален',
        ];
    }
}
