<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ScheduleMeetingRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'meetingName' => 'required|string|max:100',
            'startDateTime' => 'required|date_format:Y-m-d H:i:s',
            'endDateTime' => 'required|date_format:Y-m-d H:i:s|after_or_equal:startDateTime',
            'userIDs' => 'required|string', // userIDs will be a comma-separated string of integers
        ];
    }
}
