<?php

namespace App\Http\Requests;

use App\Rules\ExistingStatus;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\Foundation\Http\FormRequest;

class UpdateUserRequest extends FormRequest
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
            'name' => ['sometimes','required', 'string', 'min:5', 'max:255', 'alpha'],
            'surname' => ['sometimes','required', 'string', 'min:5', 'max:255', 'alpha'],
            'email' => ['sometimes','required', 'string' ,'email', 'max:255', Rule::unique('users','email')],
            'password' => ['sometimes','required', 'string' , Password::min(8)->mixedCase()->numbers()->symbols()],
            'statusId' => ['sometimes','bail', 'required', 'integer', 'gte:1', new ExistingStatus() ],
        ];
    }

    public function prepareForValidation()
    {
        if($this->statusId)
        {
            $this->merge([
                'status_id' => $this->statusId,
            ]);
        }
    }
}
