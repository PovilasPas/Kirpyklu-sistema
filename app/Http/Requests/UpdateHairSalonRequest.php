<?php

namespace App\Http\Requests;

use App\Models\HairSalon;
use App\Rules\ExistingCity;
use Illuminate\Foundation\Http\FormRequest;

class UpdateHairSalonRequest extends FormRequest
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
            'name' => ['required', 'string' ,'min:5', 'max:255', 'regex:/^[\pL\d\- ]*$/u'],
            'address' => ['required', 'string' ,'min:5', 'max:255', 'regex:/^[\pL\d\.\- ]*$/u'],
            'description' => ['required', 'string' ,'min:50', 'max:1000'],
            'cityId' => ['bail', 'required', 'integer', 'gte:1' , new ExistingCity()],
        ];
    }

    public function messages()
    {
        return [
            'name.regex' => 'Detected not allowed symbols in the name field',
            'address.regex' => 'Detected not allowed symbols in the address field'
        ];
    }

    public function prepareForValidation()
    {
        $this->merge([
            'city_id' => $this->cityId
        ]);
    }
}
