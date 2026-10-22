<?php

namespace App\Http\Requests\Auth;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name'=>['required','string','max:255'],
            'email'=>['required','string','email','max:255','unique:users'],
            'password'=>['required','string','min:8'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Bro, the name field is required.',
            'name.string' => 'Bro, the name must be a string.',
            'name.max' => 'Bro, the name may not be greater than 255 characters.',

            'email.required' => 'Bro, the email field is required.',
            'email.string' => 'Bro, the email must be a string.',
            'email.email' => 'Bro, the email must be a valid email address.',
            'email.max' => 'Bro, the email may not be greater than 255 characters.',
            'email.unique' => 'Bro, this email is already registered.',

            'password.required' => 'Bro, the password field is required.',
            'password.string' => 'Bro, the password must be a string.',
            'password.min' => 'Bro, the password must be at least 8 characters.',
        ];
    }
}
