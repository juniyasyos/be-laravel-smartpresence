<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class updateEmployeeRequest extends FormRequest
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
        $employeeId = $this->route('id');

        return [
            'full_name' => 'sometimes|required|string|max:150',
            'nip' => 'sometimes|required|string|max:50|unique:employees,nip,' . $employeeId,
            'employee_type_id' => 'sometimes|required|integer|exists:employee_types,id',
            'work_unit_id' => 'nullable|integer|exists:work_units,id',
            'position_id' => 'nullable|integer|exists:positions,id',
            'email' => 'nullable|email|max:150',
            'phone' => 'nullable|string|max:20',
            'is_active' => 'sometimes|boolean',
        ];
    }
}
