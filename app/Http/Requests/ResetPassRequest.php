<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ResetPassRequest extends FormRequest
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
            'email' => 'required|email|string|max:255',
        ];
    }


    public function messages()
    {
        return [
            'email.required' => 'Пожалуйста, укажите Email пользователя',
            'email.email' => 'Email пользователя должен быть корректным адресом',
            'email.string' => 'Email пользователя  должен быть текстом',
            'email.max' => 'Email пользователя не должен быть более 255 символов',

        ];
    }
}
