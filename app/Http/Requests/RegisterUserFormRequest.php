<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegisterUserFormRequest extends FormRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name'  =>  ['required', 'string', 'min:3'],
            'email'  =>  ['required', 'email', 'unique:users'],
            'password'  =>  ['required', 'min:8', 'max:32', 'confirmed'],
            'phone_number' => ['required', 'numeric', 'unique:users'],
        ];
    }
}
