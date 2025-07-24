<?php
// app/Http/Requests/StoreUserRequest.php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Models\User;

class StoreUserRequest extends FormRequest
{
    public function authorize()
    {
        return $this->user()->can('create', User::class);
    }

    public function rules()
    {
        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
        ];

        if ($this->user()->is_admin) {
            $rules['is_admin'] = 'sometimes|boolean';
        }

        return $rules;
    }

}
