<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class updateUserRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $userId = $this->route('id');

        return [
            'username' => 'sometimes|required|string|max:100|unique:users,username,' . $userId,
            'password' => 'nullable|string|min:8',
            'role_id' => 'sometimes|required|integer|exists:roles,id|not_in:1',
        ];
    }
}
