<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserRequest extends FormRequest
{
    private array $_rules = [
        'user.store' => [
            'user_name' => 'required|regex:/^[A-Za-z\d_-]+$/|min:3|max:255|unique:users,name',
            'user_email' => 'required|email|max:255|unique:users,email',
            'user_password' => 'required|regex:/^[!-~]+$/|min:8|max:255|confirmed',
            'user_password_confirmation' => 'required',
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
