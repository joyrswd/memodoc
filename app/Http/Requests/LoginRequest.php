<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
{
    private array $_rules = [
        'login' => [
            'name' => 'required|regex:/^[A-Za-z\d_-]+$/|max:255',
            'password' => 'required|regex:/^[!-~]+$/|max:255',
        ],
        'password.email' => [
            'email' => 'required|email|max:255|exists:users,email',
        ],
        'password.update' => [
            'token' => 'required',
            'email' => 'required|email|max:255|exists:users,email',
            'password' => 'required|min:8|max:255|confirmed',
            'password_confirmation' => 'required',
        ],
    ];

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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return $this->_rules[$this->route()->getName()];
    }




}
