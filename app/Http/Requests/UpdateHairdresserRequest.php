<?php

namespace App\Http\Requests;

use App\Rules\ExistingSalon;
use Illuminate\Foundation\Http\FormRequest;

class UpdateHairdresserRequest extends FormRequest
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
            'isApproved' => ['required', 'boolean'],
            'hairSalonId' => ['bail', 'required', 'integer', 'gte:1', new ExistingSalon()]
        ];
    }

    public function messages()
    {
        return [
            'phoneNr.regex' => 'Not a valid phone number format: allowed symbols are + and 0-9, must contain between 7 and 15 numbers'
        ];
    }

    public function prepareForValidation()
    {
        $this->merge([
            'phone_nr' => $this->phoneNr,
            'is_approved' => $this->isApproved,
            'hair_salon_id' => $this->hairSalonId
        ]);
    }
}
