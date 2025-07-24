<?php
// app/Http/Requests/UpdateUserRequest.php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use App\Models\User;

class UpdateUserRequest extends FormRequest
{
    public function authorize()
    {
        return $this->user()->can('update', $this->route('user'));
    }

    public function rules()
    {
        $userId = $this->route('user')->id;

        return [
            'name' => 'sometimes|required|string|max:255',
            'email' => ['sometimes', 'required', 'string', 'email', Rule::unique('users', 'email')->ignore($userId)],
            'password' => 'sometimes|required|string|min:8|confirmed',
        ];
    }

    protected function prepareForValidation()
    {
        if ($this->filled('password')) {
            $this->merge([
                'password' => Hash::make($this->password),
            ]);
        }
    }
}