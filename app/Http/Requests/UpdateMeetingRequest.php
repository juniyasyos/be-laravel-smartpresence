<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use App\Models\Meeting;

class UpdateMeetingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title'                    => 'sometimes|required|string|max:200',
            'organizer'                => 'nullable|string|max:150',
            'room_id'                  => 'sometimes|required|exists:meeting_rooms,id',
            'start_time'               => 'sometimes|required|date',
            'end_time'                 => 'sometimes|required|date|after:start_time',
            'participant_employee_ids'   => 'nullable|array',
            'participant_employee_ids.*' => 'exists:employees,id',
            'participant_work_unit_ids'  => 'nullable|array',
            'participant_work_unit_ids.*'=> 'exists:work_units,id',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            if ($validator->errors()->any()) {
                return;
            }

            $meetingId = $this->route('id');
            $meeting   = Meeting::find($meetingId);

            if (!$meeting) {
                $validator->errors()->add('meeting', 'Rapat tidak ditemukan.');
                return;
            }

            // Block participant changes if meeting is ongoing
            $hasParticipantChanges = $this->has('participant_employee_ids') || $this->has('participant_work_unit_ids');
            if ($meeting->status === 'berlangsung' && $hasParticipantChanges) {
                $validator->errors()->add('participants', 'Tidak dapat mengubah peserta saat rapat sedang berlangsung.');
                return;
            }

            // Check room conflict (exclude current meeting)
            $roomId    = $this->input('room_id', $meeting->room_id);
            $startTime = $this->input('start_time', $meeting->start_time);
            $endTime   = $this->input('end_time', $meeting->end_time);

            $conflict = Meeting::where('room_id', $roomId)
                ->where('id', '!=', $meetingId)
                ->where('status', '!=', 'selesai')
                ->where(function ($query) use ($startTime, $endTime) {
                    $query->where(function ($q) use ($startTime, $endTime) {
                        $q->where('start_time', '<', $endTime)
                          ->where('end_time', '>', $startTime);
                    });
                })
                ->exists();

            if ($conflict) {
                $validator->errors()->add('room_id', 'Ruang rapat sudah digunakan pada waktu yang dipilih. Silakan pilih ruang atau waktu lain.');
            }
        });
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'message' => 'Validation failed',
            'errors'  => $validator->errors(),
        ], 422));
    }
}
