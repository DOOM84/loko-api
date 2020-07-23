<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
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
            'name' => 'required|string|min:2|max:255',
            'email' => 'required|string|email|max:255|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'Пожалуйста, укажите имя пользователя',
            'name.max' => 'Имя пользователя не должно быть более 255 символов',
            'name.min' => 'Имя пользователя должно быть более 2 символов',
            'name.string' => 'Имя пользователя  должно быть текстом',
            'email.required' => 'Пожалуйста, укажите Email пользователя',
            'email.string' => 'Email пользователя  должен быть текстом',
            'email.email' => 'Email пользователя должен быть корректным адресом',
            'email.max' => 'Email пользователя не должен быть более 255 символов',
            'email.unique' => 'Пользователь с таким Email адресом уже существует',
            'password.required' => 'Пожалуйста, укажите пароль пользователя',
            'password.string' => 'Пароль пользователя  должен быть текстом',
            'password.min' => 'Пароль пользователя не должен быть менее 6 символов',
            'password.confirmed' => 'Введенные пароли не совпадают',
        ];
    }
}
