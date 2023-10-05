<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class HairdresserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'phoneNr' => ['required', 'string', 'regex:/^\+?[0-9]{7,15}$/'],
            'isApproved' => ['required', 'boolean']
        ];
    }

    public function messages()
    {
        return [
            'phoneNr.regex' => 'Not a valid phone number format'
        ];
    }

    public function prepareForValidation()
    {
        $this->merge([
            'phone_nr' => $this->phoneNr,
            'is_approved' => $this->isApproved
        ]);
    }
}
