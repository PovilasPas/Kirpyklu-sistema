<?php

namespace App\Http\Requests;

use App\Rules\ValidImage;
use Illuminate\Foundation\Http\FormRequest;

class UpdateHairstyleRequest extends FormRequest
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
            'name' => ['sometimes','required', 'string', 'min:5', 'max:255', 'regex:/^[\pL ]*$/u'],
            'price' => ['sometimes','required', 'numeric', 'gt:0', 'lt:10000000000', 'decimal:0,2'],
            'image' => ['sometimes','nullable', 'string', 'max:6000000', new ValidImage()],
            'estimatedTimeMin' => ['sometimes','required', 'integer', 'gte:1', 'lte:720']
        ];
    }

    public function messages()
    {
        return [
            'name.regex' => 'Detected not allowed symbols in the name field'
        ];
    }

    public function prepareForValidation()
    {
        if($this->estimatedTimeMin)
        {
            $this->merge([
                'estimated_time_min' => $this->estimatedTimeMin
            ]);
        }
    }
}
