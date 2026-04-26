<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreEmployeeRequest extends FormRequest
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
        return [
            'full_name' => 'required|string|max:150',
            'nip' => 'required|string|max:50|unique:employees,nip',
            'employee_type_id' => 'required|integer|exists:employee_types,id',
            'work_unit_id' => 'nullable',
            'email' => 'nullable|email|max:150',
            'phone' => 'nullable|string|max:20',
            'signature' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'is_active' => 'sometimes|boolean',
        ];
    }
}