<?php

namespace App\Http\Requests;

use App\Models\User;
use Anik\Form\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends FormRequest
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
            'email' => [
                'required',
                'email',
                Rule::unique('users', 'email')->ignore($this->user->email)
            ],
            'role' => [
                'required',
                Rule::in(User::ROLES)
            ],
            'phone' => ['nullable', 'min:10'],
            'address' => ['nullable', 'string'],
        ];
    }
}
