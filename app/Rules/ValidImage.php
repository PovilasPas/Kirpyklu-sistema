<?php

namespace App\Rules;

use Intervention\Image\Facades\Image;
use Illuminate\Contracts\Validation\Rule;

class ValidImage implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        try
        {
            Image::make($value);
            return true;
        }
        catch(\Exception $e)
        {
            return false;
        }
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'The uploaded file was not an image';
    }
}
