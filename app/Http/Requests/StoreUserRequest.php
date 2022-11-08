<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class StoreUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    protected function authorize(): bool
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    protected function rules(): array
    {
        return [
            'name' => ['required', 'string'],
            'password' => ['required', 'min:6', 'max:50'],
            'email' => ['required', 'email', 'unique:users,email'],
            'role' => [
                'required',
                Rule::in(User::ROLES)
            ],
            'phone' => ['nullable', 'min:10'],
            'address' => ['nullable', 'string'],
        ];
    }
}
