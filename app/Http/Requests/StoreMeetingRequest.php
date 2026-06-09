<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use App\Models\Meeting;

class StoreMeetingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation()
    {
        $mergeData = [];
        if ($this->has('start_time')) {
            $mergeData['start_time'] = \Carbon\Carbon::parse($this->input('start_time'))->setTimezone('Asia/Jakarta')->format('Y-m-d H:i:s');
        }
        if ($this->has('end_time')) {
            $mergeData['end_time'] = \Carbon\Carbon::parse($this->input('end_time'))->setTimezone('Asia/Jakarta')->format('Y-m-d H:i:s');
        }
        if (!empty($mergeData)) {
            $this->merge($mergeData);
        }
    }

    public function rules(): array
    {
        return [
            'title'                    => 'required|string|max:200',
            'organizer'                => 'nullable|string|max:150',
            'room_id'                  => 'required|exists:meeting_rooms,id',
            'start_time'               => 'required|date',
            'end_time'                 => 'required|date|after:start_time',
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

            // Check room conflict: same room, overlapping time
            $roomId    = $this->input('room_id');
            // Already parsed in prepareForValidation
            $startTime = $this->input('start_time');
            $endTime   = $this->input('end_time');

            // Removed: Check if start time is in the past
            // (User requested to allow scheduling meetings even if the date has passed)

            $conflict = Meeting::where('room_id', $roomId)
                ->where('status', '!=', 'dibatalkan') // Do not exclude selesai, to prevent overlap with past finalized meetings
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

            // Must have at least one participant source
            $hasEmployeeIds = !empty($this->input('participant_employee_ids'));
            $hasWorkUnitIds = !empty($this->input('participant_work_unit_ids'));

            if (!$hasEmployeeIds && !$hasWorkUnitIds) {
                $validator->errors()->add('participants', 'Harus menambahkan minimal satu peserta rapat (pilih karyawan atau divisi).');
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
