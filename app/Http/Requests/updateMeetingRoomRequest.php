<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class updateMeetingRoomRequest extends FormRequest
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
        $roomId = $this->route('id');

        return [
            'name' => 'sometimes|required|string|max:100|unique:meeting_rooms,name,' . $roomId,
            'location' => 'sometimes|required|string|max:200',
            'capacity' => 'sometimes|required|integer|min:1',
            'is_active' => 'sometimes|boolean',
        ];
    }
}
